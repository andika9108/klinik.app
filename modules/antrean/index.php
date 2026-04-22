<?php
// 1. BACKEND LOGIC - STERIL TANPA SAMPAH
ob_start(); 
require_once __DIR__ . '/../../includes/connection.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'ambil_antrean') {
        $res = array('menunggu' => array(), 'dipanggil' => array(), 'selesai' => array());
        
        try {
            // Kita tarik semua kolom termasuk NO_URUT
            $sql = "SELECT a.*, p.nama_pasien as nama_asli 
                    FROM antrean a 
                    LEFT JOIN pasien p ON a.id_pasien = p.id_pasien 
                    ORDER BY a.waktu ASC";
            $q = $conn->query($sql);
            
            while($r = $q->fetch()) {
                $namaFix = !empty($r['nama_asli']) ? $r['nama_asli'] : (!empty($r['nama_pasien']) ? $r['nama_pasien'] : "Pasien #".$r['id']);
                
                $data = array(
                    'id'      => $r['id'],
                    'no_urut' => $r['no_urut'], // INI NOMOR ANTREANNYA
                    'nama'    => htmlspecialchars($namaFix),
                    'poli'    => htmlspecialchars($r['nama_poli'])
                );

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
        $html = "";
        while($c = $chats->fetch()) {
            $class = ($c['pengirim'] == 'admin') ? 'bubble-admin' : 'bubble-other';
            $html .= "<div class='bubble $class'>".htmlspecialchars($c['pesan'])."</div>";
        }
        if (ob_get_length()) ob_end_clean();
        echo $html;
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
    :root { --biru-pro: #0f52ba; --biru-gelap: #0a3d91; }
    .wrapper-admin { max-width: 98%; margin: 20px auto; font-family: 'Segoe UI', sans-serif; }
    
    /* Grid Utama Gede */
    .dashboard-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
    .card-admin { background: white; border-radius: 15px; border: 1px solid #e2e8f0; min-height: 600px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; overflow: hidden; }
    
    .head-biru { background: var(--biru-pro); color: white; padding: 22px; text-align: center; font-weight: 800; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
    
    /* Animasi Antrean Muncul */
    @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .pasien-item { 
        padding: 20px; border-bottom: 1px solid #f1f5f9; 
        display: flex; justify-content: space-between; align-items: center; 
        animation: slideIn 0.3s ease-out;
    }
    
    /* Nomor Antrean Bulat Gede */
    .no-bulat { 
        background: #e0e7ff; color: var(--biru-pro); 
        width: 50px; height: 50px; display: flex; 
        align-items: center; justify-content: center; 
        border-radius: 50%; font-weight: 900; font-size: 1.2rem; 
        margin-right: 15px; border: 2px solid var(--biru-pro);
    }

    .info-box { display: flex; align-items: center; }
    .text-info b { display: block; font-size: 1.2rem; color: #1e293b; }
    .text-info span { font-size: 0.8rem; color: #64748b; font-weight: bold; text-transform: uppercase; }

    .btn-biru-pro { background: var(--biru-pro); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s; }
    .btn-biru-pro:hover { background: var(--biru-gelap); transform: scale(1.05); }

    /* Chat Section */
    .chat-container { background: white; border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; }
    .chat-area { height: 300px; overflow-y: auto; padding: 25px; background: #f8fafc; display: flex; flex-direction: column; gap: 12px; }
    .bubble { max-width: 75%; padding: 12px 18px; border-radius: 18px; font-size: 1rem; }
    .bubble-admin { align-self: flex-end; background: var(--biru-pro); color: white; border-bottom-right-radius: 2px; }
    .bubble-other { align-self: flex-start; background: white; border: 1px solid #ddd; border-bottom-left-radius: 2px; }
    .input-box { padding: 20px; display: flex; gap: 10px; background: white; border-top: 1px solid #eee; }
    .input-box input { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 12px; outline: none; }
</style>

<div class="wrapper-admin">
    <div class="dashboard-grid">
        <div class="card-admin">
            <div class="head-biru">Menunggu</div>
            <div id="box-menunggu"></div>
        </div>
        <div class="card-admin" style="border: 2px solid var(--biru-pro);">
            <div class="head-biru" style="background: var(--biru-gelap);">Dipanggil</div>
            <div id="box-dipanggil"></div>
        </div>
        <div class="card-admin">
            <div class="head-biru">Selesai</div>
            <div id="box-selesai"></div>
        </div>
    </div>

    <div class="chat-container">
        <div class="head-biru" style="text-align: left; padding-left: 30px;">💬 Chat Konsultasi</div>
        <div class="chat-area" id="screenChat"></div>
        <div class="input-box">
            <input type="text" id="msgAdmin" placeholder="Balas pasien..." onkeypress="if(event.key === 'Enter') kirim()">
            <button onclick="kirim()" class="btn-biru-pro">Kirim</button>
        </div>
    </div>
</div>

<script>
    var api = 'index.php';

    function loadData() {
        fetch(api + '?action=ambil_antrean').then(r => r.json()).then(data => {
            render('box-menunggu', data.menunggu, 'Panggil', 'Dipanggil');
            render('box-dipanggil', data.dipanggil, 'Selesai', 'Selesai');
            render('box-selesai', data.selesai, '', '');
        });

        fetch(api + '?action=ambil_chat').then(r => r.text()).then(html => {
            var b = document.getElementById('screenChat');
            var isDown = b.scrollHeight - b.clientHeight <= b.scrollTop + 60;
            b.innerHTML = html;
            if(isDown) b.scrollTop = b.scrollHeight;
        });
    }

    function render(target, list, label, next) {
        var h = '';
        if (list.length === 0) h = '<p style="text-align:center; color:#999; margin-top:50px;">Tidak ada antrean</p>';
        for (var i = 0; i < list.length; i++) {
            var p = list[i];
            h += '<div class="pasien-item">';
            h += '<div class="info-box">';
            h += '<div class="no-bulat">'+p.no_urut+'</div>';
            h += '<div class="text-info"><b>'+p.nama+'</b><span>'+p.poli+'</span></div>';
            h += '</div>';
            if (label !== '') {
                h += '<button class="btn-biru-pro" onclick="upStatus('+p.id+', \''+next+'\')">'+label+'</button>';
            } else {
                h += '<b style="color:var(--biru-pro);">BERES ✓</b>';
            }
            h += '</div>';
        }
        document.getElementById(target).innerHTML = h;
    }

    function upStatus(id, s) {
        fetch(api + '?action=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id + '&status=' + s
        }).then(() => loadData());
    }

    function kirim() {
        var i = document.getElementById('msgAdmin');
        if(!i.value.trim()) return;
        fetch(api + '?action=balas_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pesan_admin=' + encodeURIComponent(i.value.trim())
        }).then(() => { i.value = ''; loadData(); });
    }

    setInterval(loadData, 3000);
    window.onload = loadData;
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>