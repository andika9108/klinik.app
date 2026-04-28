<?php
// 1. BACKEND LOGIC (PHP 5.6 Compatible)
require_once __DIR__ . '/../../includes/connection.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // API: Ambil Semua Data Antrean
    if ($action == 'ambil_antrean') {
        try {
            // --- LOGIC PEMBERSIH OTOMATIS (Hanya untuk yang 'Selesai') ---
            $conn->query("DELETE FROM antrean WHERE status = 'Selesai' AND id_antrean NOT IN (
                SELECT id_antrean FROM (
                    SELECT id_antrean FROM antrean 
                    WHERE status = 'Selesai' 
                    ORDER BY id_antrean DESC LIMIT 10
                ) tmp
            )");
            // ------------------------------------------------------------

            // URUTAN DIPERBAIKI: Utamakan nomor urut terkecil (no_urut) secara global
            $sql = "SELECT a.*, p.nama_poli, ps.nama_pasien 
                    FROM antrean a 
                    LEFT JOIN poli p ON a.id_poli = p.id_poli 
                    LEFT JOIN pasien ps ON a.id_pasien = ps.id_pasien
                    ORDER BY a.no_urut ASC, a.id_poli ASC";
            $q = $conn->query($sql);
        } catch (Exception $e) {
            $q = $conn->query("SELECT *, 'Error' as nama_poli, 'Pasien' as nama_pasien FROM antrean ORDER BY no_urut ASC");
        }
        
        $res = array('menunggu' => array(), 'proses' => array(), 'selesai' => array());

        while($r = $q->fetch()) {
            $data = array(
                'id'      => $r['id_antrean'],
                'nama'    => htmlspecialchars($r['nama_pasien'] ?: 'Tanpa Nama'),
                'poli'    => $r['nama_poli'] ?: 'Umum',
                'no_urut' => $r['no_urut']
            );

            $s = strtolower($r['status']);
            if ($s == 'proses') {
                $res['proses'][] = $data;
            } elseif ($s == 'selesai') {
                // Pake array_unshift supaya yang nomornya gede (terbaru) muncul di paling atas kolom Selesai
                array_unshift($res['selesai'], $data);
            } else {
                $res['menunggu'][] = $data;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    // API: Update Status Antrean
    if ($action == 'update_status' && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE antrean SET status = ? WHERE id_antrean = ?");
        $stmt->execute(array($_POST['status'], $_POST['id']));
        echo json_encode(array('status' => 'success'));
        exit;
    }

    // API: Ambil Chat
    if ($action == 'ambil_chat') {
        $chats = $conn->query("SELECT * FROM chat_konsultasi ORDER BY waktu ASC");
        while($c = $chats->fetch()) {
            $class = ($c['pengirim'] == 'admin') ? 'bubble-admin' : 'bubble-other';
            echo "<div class='bubble $class'>".htmlspecialchars($c['pesan'])."</div>";
        }
        exit;
    }

    // API: Kirim Chat (Manual)
    if ($action == 'balas_chat' && isset($_POST['pesan_admin'])) {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute(array($_POST['pesan_admin']));
        echo json_encode(array('status' => 'success'));
        exit;
    }

   // API: Balas Chat Pake Groq AI
    if ($action == 'balas_chat_ai') {
        $apiKey = "#"; 
        
        $cek = $conn->query("SELECT pesan FROM chat_konsultasi WHERE pengirim != 'admin' ORDER BY id_chat DESC LIMIT 1");
        $lastChat = $cek->fetch(PDO::FETCH_ASSOC);
        $pesanPasien = $lastChat ? $lastChat['pesan'] : "Halo admin.";

        $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
        $systemPrompt = "Kamu adalah asisten admin klinik. Jawab singkat maksimal 2 kalimat.";

        $data = [
            "model" => "llama3-70b-8192", 
            "messages" => [
                ["role" => "system", "content" => $systemPrompt],
                ["role" => "user", "content" => $pesanPasien]
            ]
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "Content-Type: application/json"
        ]);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $resJson = json_decode($response, true);
        $aiReply = isset($resJson['choices'][0]['message']['content']) ? trim($resJson['choices'][0]['message']['content']) : "Maaf, AI sedang offline.";

        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute(["✨ [AI]: " . $aiReply]);

        echo json_encode(['status' => 'success']);
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<style>
    :root { 
        --primary: #0f52ba; 
        --waiting: #94a3b8; 
        --calling: #f59e0b; 
        --done: #10b981; 
        --bg: #f8fafc;
    }
    .wrapper { max-width: 1200px; margin: 20px auto; padding: 0 15px; font-family: 'Segoe UI', sans-serif; }
    .grid-antrean { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px; }
    .col-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; min-height: 450px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .col-header { padding: 15px; color: white; font-weight: bold; text-align: center; border-radius: 11px 11px 0 0; font-size: 0.9rem; letter-spacing: 1px; }
    .patient-box { padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; }
    .patient-box:hover { background: #fdfdfd; }
    .info b { display: block; font-size: 1rem; color: #1e293b; margin-bottom: 2px; }
    .info span { font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
    .btn-act { border: none; padding: 10px 18px; border-radius: 8px; color: white; cursor: pointer; font-weight: bold; font-size: 0.8rem; transition: 0.2s; }
    .chat-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-top: 20px; }
    .chat-box { height: 250px; overflow-y: auto; padding: 15px; background: var(--bg); display: flex; flex-direction: column; gap: 10px; }
    .bubble { max-width: 80%; padding: 10px 15px; border-radius: 12px; font-size: 0.9rem; }
    .bubble-admin { align-self: flex-end; background: var(--primary); color: white; }
    .bubble-other { align-self: flex-start; background: white; border: 1px solid #e2e8f0; }
    .input-area { padding: 15px; background: white; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; }
    .input-area input { flex: 1; padding: 12px; border: 1px solid #cbd5e1; border-radius: 10px; outline: none; }
</style>

<div class="wrapper">
    <div style="text-align: center; margin-bottom: 25px;">
        <h2 style="color: #1e293b; margin-bottom: 5px;">Manajemen Antrean Klinik</h2>
        <p style="color: #64748b;">Pantau dan kelola urutan pasien secara real-time.</p>
    </div>

    <div class="grid-antrean">
        <div class="col-card">
            <div class="col-header" style="background: var(--waiting);">MENUNGGU</div>
            <div id="list-menunggu"></div>
        </div>

        <div class="col-card" style="border: 2px solid var(--calling); transform: scale(1.02);">
            <div class="col-header" style="background: var(--calling);">SEDANG DIPANGGIL (PROSES)</div>
            <div id="list-proses"></div>
        </div>

        <div class="col-card">
            <div class="col-header" style="background: var(--done);">SELESAI</div>
            <div id="list-selesai"></div>
        </div>
    </div>

    <div class="chat-card">
        <div class="col-header" style="background: var(--primary); text-align: left; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></span>
                Live Chat Admin
            </div>
            <small style="color:#cbd5e1; font-weight:normal;">Powered by Groq AI</small>
        </div>
        <div class="chat-box" id="chatMonitor"></div>
        
        <div class="input-area">
            <input type="text" id="msg" placeholder="Ketik balasan untuk pasien..." onkeypress="if(event.key === 'Enter') kirim()">
            <button type="button" onclick="kirim()" style="background:var(--primary); color:white; border:none; padding:0 25px; border-radius:10px; font-weight:bold; cursor:pointer;">Kirim</button>
            <button type="button" onclick="kirimAI()" id="btn-ai" style="background:#8b5cf6; color:white; border:none; padding:0 20px; border-radius:10px; font-weight:bold; cursor:pointer; display:flex; align-items:center; gap:5px;">✨ Auto AI</button>
        </div>
    </div>
</div>

<script>
    var currentPath = 'index.php';

    function loadAll() {
        fetch(currentPath + '?action=ambil_antrean')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            renderList('list-menunggu', data.menunggu, 'Panggil/Proses', 'Proses', 'var(--primary)');
            renderList('list-proses', data.proses, 'Selesaikan', 'Selesai', 'var(--done)');
            renderList('list-selesai', data.selesai, '', '', '');
        });

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
            html = '<p style="text-align:center; color:#ccc; margin-top:50px; font-size:0.8rem;">Tidak ada antrean</p>';
        }
        
        for (var i = 0; i < items.length; i++) {
            var p = items[i];
            html += '<div class="patient-box">';
            html += '   <div class="info">';
            html += '       <b>#' + p.no_urut + ' - ' + p.nama + '</b>';
            html += '       <span>' + p.poli + '</span>';
            html += '   </div>';
            
            if (btnLabel !== '') {
                html += '   <button class="btn-act" style="background:'+btnBg+'" onclick="updateStatus('+p.id+', \''+nextStatus+'\')">'+btnLabel+'</button>';
            } else {
                html += '   <small style="color:var(--done); font-weight:bold; font-size: 0.7rem;">SELESAI ✓</small>';
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
        }).then(function() { 
            loadAll(); 
        });
    }

    function kirim() {
        var inp = document.getElementById('msg');
        if(!inp.value.trim()) return;
        fetch(currentPath + '?action=balas_chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pesan_admin=' + encodeURIComponent(inp.value.trim())
        }).then(function() { 
            inp.value = ''; 
            loadAll(); 
        });
    }

    function kirimAI() {
        var inp = document.getElementById('msg');
        var btnAI = document.getElementById('btn-ai');
        inp.placeholder = "AI sedang berpikir...";
        inp.disabled = true;
        btnAI.innerHTML = "⏳ Wait...";
        btnAI.style.opacity = "0.7";
        fetch(currentPath + '?action=balas_chat_ai', { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            inp.placeholder = "Ketik balasan untuk pasien...";
            inp.disabled = false;
            btnAI.innerHTML = "✨ Auto AI";
            btnAI.style.opacity = "1";
            loadAll(); 
        }).catch(function() {
            alert("Gagal memanggil AI.");
            inp.placeholder = "Ketik balasan untuk pasien...";
            inp.disabled = false;
            btnAI.innerHTML = "✨ Auto AI";
            btnAI.style.opacity = "1";
        });
    }  

    setInterval(loadAll, 3000);
    window.onload = loadAll;
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>