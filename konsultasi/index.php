<?php 
// Cuma pakai satu ../ karena sekarang posisinya ada di folder konsultasi/
include '../includes/header.php'; 
?>

<style>
    .chat-container {
        max-width: 600px; margin: 40px auto; background: white; border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #eaeaea;
        display: flex; flex-direction: column; height: 550px;
    }
    .chat-header {
        background: #0f52ba; color: white; padding: 15px 20px; display: flex; align-items: center; gap: 15px;
    }
    .chat-box {
        flex: 1; padding: 20px; overflow-y: auto; background: #f4f6f8; display: flex; flex-direction: column; gap: 15px;
    }
    .message { 
        max-width: 80%; padding: 12px 16px; border-radius: 12px; line-height: 1.5; font-size: 0.95rem; 
    }
    .msg-ai { 
        background: white; border: 1px solid #ddd; align-self: flex-start; border-bottom-left-radius: 2px; color: #333;
    }
    .msg-user { 
        background: #0f52ba; color: white; align-self: flex-end; border-bottom-right-radius: 2px; 
    }
    .chat-input-area {
        padding: 15px; background: white; border-top: 1px solid #eaeaea; display: flex; gap: 10px;
    }
    .chat-input {
        flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-family: inherit;
    }
    .chat-input:focus { border-color: #0f52ba; }
    .btn-send {
        background: #0f52ba; color: white; border: none; padding: 0 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.2s;
    }
    .btn-send:hover { background: #0b3d8c; }
</style>

<div class="chat-container">
    <div class="chat-header">
        <div style="font-size: 2rem;">🤖</div>
        <div>
            <h2 style="margin: 0; font-size: 1.2rem;">AI Asisten Medis</h2>
            <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">Online - Siap membantu keluhan ringan Anda</p>
        </div>
    </div>

    <div class="chat-box" id="chatBox">
        <div class="message msg-ai">
            Halo! Saya Asisten AI Klinik Sehat Digital. Ada keluhan kesehatan apa yang sedang Anda rasakan hari ini? Silakan ceritakan keluhan Anda.
        </div>
    </div>

    <div class="chat-input-area">
        <input type="text" id="userInput" class="chat-input" placeholder="Ketik keluhan Anda di sini..." onkeypress="handleKeyPress(event)">
        <button onclick="kirimPesan()" class="btn-send">Kirim</button>
    </div>
</div>

<div style="text-align: center; margin-bottom: 50px;">
    <a href="../dashboard.php" style="color: #666; text-decoration: none; font-weight: bold;">&larr; Kembali ke Beranda</a>
</div>

<script>
    function kirimPesan() {
        var inputField = document.getElementById("userInput");
        var pesan = inputField.value.trim();
        if (pesan === "") return;

        var chatBox = document.getElementById("chatBox");
        var userBubble = document.createElement("div");
        userBubble.className = "message msg-user";
        userBubble.innerText = pesan;
        chatBox.appendChild(userBubble);

        inputField.value = "";
        chatBox.scrollTop = chatBox.scrollHeight;

        setTimeout(function() {
            var aiBubble = document.createElement("div");
            aiBubble.className = "message msg-ai";
            
            var balasan = "Terima kasih atas informasinya. Saat ini sistem AI kami masih dalam tahap pengembangan. Namun, keluhan mengenai '" + pesan + "' sebaiknya segera dikonsultasikan dengan dokter spesialis kami melalui menu pendaftaran antrean.";
            
            if(pesan.toLowerCase().includes("pusing") || pesan.toLowerCase().includes("sakit kepala")) {
                balasan = "Untuk keluhan pusing atau sakit kepala, pastikan Anda mendapatkan istirahat yang cukup dan minum air putih. Jika rasa sakit tidak tertahankan atau berlangsung lebih dari dua hari, segera ambil nomor antrean Poli Umum untuk pemeriksaan lebih lanjut.";
            }

            aiBubble.innerText = balasan;
            chatBox.appendChild(aiBubble);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 1000);
    }

    function handleKeyPress(e) {
        if (e.key === "Enter") {
            kirimPesan();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>