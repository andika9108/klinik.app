<?php
require_once '../includes/connection.php';

if (isset($_POST['pesan'])) {
    $pesanPasien = $_POST['pesan'];

    try {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('pasien', ?)");
        $stmt->execute([$pesanPasien]);

        // PAKAI KEY BARU LU DI SINI
        $apiKey = "#"; 
        
        // GANTI KE MODEL TERBARU
        $model = "llama-3.3-70b-versatile"; 

        $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
        $data = [
            "model" => $model,
            "messages" => [
                ["role" => "system", "content" => "Kamu asisten klinik ramah. Jawab maksimal 2 kalimat."],
                ["role" => "user", "content" => $pesanPasien]
            ]
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $apiKey", "Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $resJson = json_decode($response, true);
        curl_close($ch);

        // Ambil jawaban asli atau pesan error
        if (isset($resJson['choices'][0]['message']['content'])) {
            $aiReply = $resJson['choices'][0]['message']['content'];
        } elseif (isset($resJson['error']['message'])) {
            $aiReply = "⚠️ Groq Error: " . $resJson['error']['message'];
        } else {
            $aiReply = "Maaf, sistem AI sedang update.";
        }

        $stmtAI = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmtAI->execute(["✨ [AI]: " . $aiReply]);

        echo "success";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>