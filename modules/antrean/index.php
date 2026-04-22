<?php
// 1. BACKEND LOGIC (PHP 5.6 Compatible)
require_once __DIR__ . '/../../includes/connection.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'ambil_antrean') {
        // QUERY SAKTI: Kita ambil semua data antrean. 
        // Kalau JOIN poli bikin eror, kita pake query sederhana aja biar aman.
        try {
            $sql = "SELECT a.*, p.nama_poli 
                    FROM antrean a 
                    LEFT JOIN poli p ON a.id_poli = p.id 
                    ORDER BY a.waktu_daftar ASC";
            $q = $conn->query($sql);
        } catch (Exception $e) {
            // Backup kalau JOIN gagal karena kolom id_poli gak ada
            $q = $conn->query("SELECT *, 'Umum' as nama_poli FROM antrean ORDER BY id ASC");
        }
        
        $res = array('menunggu' => array(), 'dipanggil' => array(), 'selesai' => array());

        while($r = $q->fetch()) {
            // DETEKSI NAMA: Cari kolom nama_pasien atau nama
            $namaRaw = isset($r['nama_pasien']) ? $r['nama_pasien'] : (isset($r['nama']) ? $r['nama'] : 'Tanpa Nama');
            
            $data = array(
                'id'    => isset($r['id']) ? $r['id'] : 0,
                'nama'  => htmlspecialchars($namaRaw),
                'poli'  => isset($r['nama_poli']) ? $r['nama_poli'] : 'Umum'
            );

            // Kelompokkan berdasarkan status
            $s = strtolower($r['status']);
            if ($s == 'dipanggil') {
                $res['dipanggil'][] = $data;
            } elseif ($s == 'selesai') {
                $res['selesai'][] = $data;
            } else {
                $res['menunggu'][] = $data;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    if ($action == 'update_status' && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE antrean SET status = ? WHERE id = ?");
        $stmt->execute(array($_POST['status'], $_POST['id']));
        echo json_encode(array('status' => 'success'));
        exit;
    }

    if ($action == 'ambil_chat') {
        $chats = $conn->query("SELECT * FROM chat_konsultasi ORDER BY waktu ASC");
        while($c = $chats->fetch()) {
            $class = ($c['pengirim'] == 'admin') ? 'bubble-admin' : 'bubble-other';
            echo "<div class='bubble $class'>".htmlspecialchars($c['pesan'])."</div>";
        }
        exit;
    }

    if ($action == 'balas_chat' && isset($_POST['pesan_admin'])) {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute(array($_POST['pesan_admin']));
        echo json_encode(array('status' => 'success'));
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    :root { --primary: #0f52ba; --waiting: #94a3b8; --calling: #f59e0b; --done: #10b981; }
    .wrapper { max-width: 1200px; margin: 20px auto; padding: 0 15px; font-family: 'Segoe UI', sans-serif; }
    
    /* Layout 3 Kolom */
    .grid-antrean { display: grid; grid-template-columns: 1fr 1.2fr 1fr; gap: 20px; margin-bottom: 30px; }
    
    .col-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; min-height: 400px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .col-header { padding: 15px; color: white; font-weight: bold; text-align: center; border-radius: 11px 11px 0 0; font-size: 0.9rem; }
    
    .patient-box { padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .info b { display: block; font-size: 1rem; color: #1e293b; }
    .info span { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; }

    .btn-act { border: none; padding: 8px 15px; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; font-size: 0.8rem; }
    
    /* Chat */
    .chat-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
    .chat-box { height: 250px; overflow-y: auto; padding: 15px; background: #f8fafc; display: flex; flex-direction: column; gap: 10px; }
    .bubble { max-width: 80%; padding: 10px 15px; border-radius: 12px; font-size: 0.9rem; }
    .bubble-admin { align-self: flex-end; background: var(--primary); color: white; border-bottom-right-radius: 2px; }
    .bubble-other { align-self: flex-start; background: white; border: 1px solid #e2e8f0; border-bottom-left-radius: 2px; }
    .input-area { padding: 15px; background: white; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; }
    .input-area input { flex: 1; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; outline: none; }
</style>

<div class="wrapper">
    <div class="grid-antrean">
        <div class="col-card">
            <div class="col-header" style="background: var(--waiting);">MENUNGGU</div>
            <div id="list-menunggu"></div>
        </div>

        <div class="col-card" style="border: 2px solid var(--calling); transform: scale(1.02);">
            <div class="col-header" style="background: var(--calling);">SEDANG DIPANGGIL</div>
            <div id="list-dipanggil"></div>
        </div>

        <div class="col-card">
            <div class="col-header" style="background: var(--done);">SELESAI</div>
            <div id="list-selesai"></div>
        </div>
    </div>

    <div class="chat-card">
        <div class="col-header" style="background: var(--primary); text-align: left;">Live Chat Admin</div>
        <div class="chat-box" id="chatMonitor"></div>
        <div class="input-area">
            <input type="text" id="msg" placeholder="Balas pasien..." onkeypress="if(event.key === 'Enter') kirim()">
            <button type="button" onclick="kirim()" style="background:var(--primary); color:white; border:none; padding:0 25px; border-radius:10px; font-weight:bold; cursor:pointer;">Kirim</button>
        </div>
    </div>
</div>

<script>
    var currentPath = 'index.php';

    function loadAll() {
        // Ambil data Antrean
        fetch(currentPath + '?action=ambil_antrean')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderList('list-menunggu', data.menunggu, 'Panggil', 'Dipanggil', 'var(--primary)');
            renderList('list-dipanggil', data.dipanggil, 'Selesai', 'Selesai', 'var(--done)');
            renderList('list-selesai', data.selesai, '', '', '');
        });

        // Ambil data Chat
        fetch(currentPath + '?action=ambil_chat')
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var box = document.getElementById('chatMonitor');
            var isBottom = box.scrollHeight - box.clientHeight <= box.scrollTop + 50;
            box.innerHTML = html;
            if(isBottom) box.scrollTop = box.scrollHeight;
        });
    }

    function renderList(targetId, items, btnLabel, nextStatus, btnBg) {
        var html = '';
        if (items.length === 0) {
            html = '<p style="text-align:center; color:#ccc; margin-top:30px; font-size:0.8rem;">Kosong</p>';
        }
        for (var i = 0; i < items.length; i++) {
            var p = items[i];
            html += '<div class="patient-box">';
            html += '<div class="info"><b>'+p.nama+'</b><span>'+p.poli+'</span></div>';
            if (btnLabel !== '') {
                html += '<button class="btn-act" style="background:'+btnBg+'" onclick="updateStatus('+p.id+', \''+nextStatus+'\')">'+btnLabel+'</button>';
            } else {
                html += '<small style="color:var(--done); font-weight:bold;">Selesai ✓</small>';
            }
            html += '</div>';
        }
        document.getElementById(targetId).innerHTML = html;
    }

    function updateStatus(id, stat) {
        fetch(currentPath + '?action=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id + '&status=' + stat
        }).then(function() { loadAll(); });
    }

    function kirim() {
        var inp = document.getElementById('msg');
        if(!inp.value.trim()) return;
        fetch(currentPath + '?action=balas_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pesan_admin=' + encodeURIComponent(inp.value.trim())
        }).then(function() { inp.value = ''; loadAll(); });
    }

    setInterval(loadAll, 3000);
    window.onload = loadAll;
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>