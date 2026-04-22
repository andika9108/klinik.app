<?php
// 1. BACKEND LOGIC - HARUS DI BARIS PERTAMA, TANPA SPASI SEBELUM TAG PHP
ob_start(); 
require_once __DIR__ . '/../../includes/connection.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'ambil_antrean') {
        $res = array('menunggu' => array(), 'dipanggil' => array(), 'selesai' => array());
        
        try {
            // QUERY JOIN sesuai contoh poli/index.php lu
            $sql = "SELECT a.*, p.nama_pasien as nama_asli 
                    FROM antrean a 
                    LEFT JOIN pasien p ON a.id_pasien = p.id_pasien 
                    ORDER BY a.waktu ASC";
            $q = $conn->query($sql);
            
            while($r = $q->fetch()) {
                // Ambil nama dari tabel pasien, kalo kosong pake nama dari tabel antrean
                $namaFix = !empty($r['nama_asli']) ? $r['nama_asli'] : (!empty($r['nama_pasien']) ? $r['nama_pasien'] : "Pasien #".$r['id']);
                
                $data = array(
                    'id'    => $r['id'],
                    'nama'  => htmlspecialchars($namaFix),
                    'poli'  => htmlspecialchars($r['nama_poli'])
                );

                // Normalisasi status biar gak error gara-gara besar kecil huruf
                $status = strtolower(trim($r['status']));
                if ($status == 'dipanggil') {
                    $res['dipanggil'][] = $data;
                } elseif ($status == 'selesai') {
                    $res['selesai'][] = $data;
                } else {
                    $res['menunggu'][] = $data;
                }
            }
        } catch (Exception $e) { $res['error'] = $e->getMessage(); }

        // BUANG SEMUA OUTPUT SEBELUMNYA (SPASI/ERROR)
        if (ob_get_length()) ob_end_clean(); 
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    if ($action == 'update_status' && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE antrean SET status = ? WHERE id = ?");
        $stmt->execute(array($_POST['status'], $_POST['id']));
        if (ob_get_length()) ob_end_clean();
        echo json_encode(array('status' => 'success'));
        exit;
    }

    if ($action == 'ambil_chat') {
        $chats = $conn->query("SELECT * FROM chat_konsultasi ORDER BY waktu ASC");
        $htmlChat = "";
        while($c = $chats->fetch()) {
            $class = ($c['pengirim'] == 'admin') ? 'bubble-admin' : 'bubble-other';
            $htmlChat .= "<div class='bubble $class'>".htmlspecialchars($c['pesan'])."</div>";
        }
        if (ob_get_length()) ob_end_clean();
        echo $htmlChat;
        exit;
    }

    if ($action == 'balas_chat' && isset($_POST['pesan_admin'])) {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute(array($_POST['pesan_admin']));
        if (ob_get_length()) ob_end_clean();
        echo json_encode(array('status' => 'success'));
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    :root { --biru-pro: #0f52ba; }
    .container-admin { max-width: 98%; margin: 20px auto; font-family: 'Segoe UI', sans-serif; }
    
    /* Grid 3 Kolom - GEDE & LEGA */
    .grid-utama { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 30px; }
    .kartu-status { background: white; border-radius: 15px; border: 1px solid #e0e0e0; min-height: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; }
    
    /* Header Biru Solid */
    .head-biru { background: var(--biru-pro); color: white; padding: 25px; text-align: center; font-weight: 800; font-size: 1.3rem; text-transform: uppercase; letter-spacing: 1px; }
    
    .list-pasien { padding: 25px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .list-pasien:hover { background: #f9fbff; }
    .box-info b { display: block; font-size: 1.3rem; color: #333; margin-bottom: 5px; }
    .box-info span { font-size: 0.9rem; color: white; background: #5c6bc0; padding: 4px 12px; border-radius: 6px; font-weight: 600; }

    .btn-konfirmasi { background: var(--biru-pro); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: bold; cursor: pointer; font-size: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .btn-konfirmasi:hover { background: #083d8d; }

    /* Chat Section */
    .kartu-chat { background: white; border-radius: 15px; border: 1px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
    .area-chat { height: 350px; overflow-y: auto; padding: 25px; background: #f8fafc; display: flex; flex-direction: column; gap: 12px; }
    .bubble { max-width: 70%; padding: 15px 20px; border-radius: 18px; font-size: 1rem; line-height: 1.4; }
    .bubble-admin { align-self: flex-end; background: var(--biru-pro); color: white; border-bottom-right-radius: 2px; }
    .bubble-other { align-self: flex-start; background: white; border: 1px solid #ddd; color: #333; border-bottom-left-radius: 2px; }
    .chat-input-box { padding: 20px; display: flex; gap: 15px; background: white; border-top: 1px solid #eee; }
    .chat-input-box input { flex: 1; padding: 15px; border: 2px solid #ddd; border-radius: 12px; outline: none; font-size: 1rem; }
</style>

<div class="container-admin">
    <div class="grid-utama">
        <div class="kartu-status">
            <div class="head-biru">Menunggu Konfirmasi</div>
            <div id="list-menunggu"></div>
        </div>
        <div class="kartu-status" style="border: 2px solid var(--biru-pro);">
            <div class="head-biru" style="background: #0d47a1;">Sedang Dipanggil</div>
            <div id="list-dipanggil"></div>
        </div>
        <div class="kartu-status">
            <div class="head-biru" style="background: #0a3d91;">Selesai</div>
            <div id="list-selesai"></div>
        </div>
    </div>

    <div class="kartu-chat">
        <div class="head-biru" style="text-align: left; padding-left: 30px;">💬 Live Chat Konsultasi</div>
        <div class="area-chat" id="chatScreen"></div>
        <div class="chat-input-box">
            <input type="text" id="adminInput" placeholder="Balas chat pasien..." onkeypress="if(event.key === 'Enter') kirim()">
            <button onclick="kirim()" class="btn-konfirmasi">Kirim Pesan</button>
        </div>
    </div>
</div>

<script>
    var base = 'index.php';

    function loadLive() {
        // AMBIL ANTREAN
        fetch(base + '?action=ambil_antrean')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderList('list-menunggu', data.menunggu, 'Panggil', 'Dipanggil');
            renderList('list-dipanggil', data.dipanggil, 'Selesaikan', 'Selesai');
            renderList('list-selesai', data.selesai, '', '');
        }).catch(e => console.log("Gagal memproses JSON."));

        // AMBIL CHAT
        fetch(base + '?action=ambil_chat')
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var s = document.getElementById('chatScreen');
            var isDown = s.scrollHeight - s.clientHeight <= s.scrollTop + 65;
            s.innerHTML = html;
            if(isDown) s.scrollTop = s.scrollHeight;
        });
    }

    function renderList(id, items, label, stat) {
        var html = '';
        if (items.length === 0) {
            html = '<div style="text-align:center; margin-top:80px; color:#aaa;"><p style="font-size:3.5rem; margin:0;">☕</p><p>Tidak ada data</p></div>';
        }
        for (var i = 0; i < items.length; i++) {
            var p = items[i];
            html += '<div class="list-pasien">';
            html += '<div class="box-info"><b>'+p.nama+'</b><span>'+p.poli+'</span></div>';
            if (label !== '') {
                html += '<button class="btn-konfirmasi" onclick="gantiStat('+p.id+', \''+stat+'\')">'+label+'</button>';
            } else {
                html += '<b style="color:var(--biru-pro); font-size:1.1rem;">BERES ✓</b>';
            }
            html += '</div>';
        }
        document.getElementById(id).innerHTML = html;
    }

    function gantiStat(id, s) {
        fetch(base + '?action=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id + '&status=' + s
        }).then(function() { loadLive(); });
    }

    function kirim() {
        var inp = document.getElementById('adminInput');
        if(!inp.value.trim()) return;
        fetch(base + '?action=balas_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pesan_admin=' + encodeURIComponent(inp.value.trim())
        }).then(function() { inp.value = ''; loadLive(); });
    }

    setInterval(loadLive, 3000);
    window.onload = loadLive;
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>