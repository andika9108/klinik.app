<?php
require_once __DIR__ . '/../../includes/connection.php';

if (!empty($_POST['pesan_admin'])) {
    $pesan = trim($_POST['pesan_admin']);
    
    try {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute([$pesan]);
        
        // Kirim respon sukses ke JavaScript
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error']);
    }
}
?>