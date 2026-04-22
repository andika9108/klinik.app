<?php 
include '../includes/header.php'; 
// Pastikan koneksi dipanggil
require_once '../includes/connection.php';
?>

<style>
    .chat-container { max-width: 600px; margin: 40px auto; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eaeaea; display: flex; flex-direction: column; height: 550px; }
    .chat-header { background: #0f52ba; color: white; padding: 15px 20px; display: flex; align-items: center; gap: 15px; }
    .chat-box { flex: 1; padding: 20px; overflow-y: auto; background: #f4f6f8; display: flex; flex-direction: column; gap: 15px; }
    .message { max-width: 80%; padding: 12px 16px; border-radius: 12px; line-height: 1.5; font-size: 0.95rem; position: relative; }
    .msg-admin { background: white; border: 1px solid #ddd; align-self: flex-start; border-bottom-left-radius: 2px; color: #333; }
    .msg-user { background: #0f52ba; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
    .chat-input-area { padding: 15px; background: white; border-top: 1px solid #eaeaea; display: flex; gap: 10px; }
    .chat-input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
    .btn-send { background: #0f52ba; color: white; border: none; padding: 0 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }
    .time { font-size: 0.7rem; display: block; margin-top: 5px; opacity: 0.7; }
</style>

<div class="chat-container">
    <div class="chat-header">
        <div style="font-size: 2rem;">👩‍⚕️</div>
        <div>
            <h2 style="margin: 0; font-size: 1.2rem;">Chat Konsultasi Admin</h2>
            <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">Klinik Sehat Digital</p>
        </div>
    </div>

    <div class="chat-box" id="chatBox">
        </div>

    <div class="chat-input-area">
        <input type="text" id="userInput" class="chat-input" placeholder="Ketik pesan ke admin..." onkeypress="handleKeyPress(event)">
        <button id="btnKirim" onclick="kirimPesan()" class="btn-send">Kirim</button>
    </div>
</div>

<script>
    // Fungsi Ambil Chat Otomatis (Real-time)
    function muatChat() {
        fetch('ambil_chat.php')
        .then(response => response.text())
        .then(html => {
            const chatBox = document.getElementById("chatBox");
            // Cek kalau scroll lagi di bawah, biar otomatis scroll pas ada pesan baru
            const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 1;
            
            chatBox.innerHTML = html;
            
            if (isAtBottom) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
    }

    // Jalankan setiap 2 detik
    setInterval(muatChat, 2000);
    muatChat();

    function kirimPesan() {
        var inputField = document.getElementById("userInput");
        var pesan = inputField.value.trim();
        if (pesan === "") return;

        fetch('proses_chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'pesan=' + encodeURIComponent(pesan)
        })
        .then(() => {
            inputField.value = "";
            muatChat();
        });
    }

    function handleKeyPress(e) {
        if (e.key === "Enter") kirimPesan();
    }
</script>

<?php include '../includes/footer.php'; ?>