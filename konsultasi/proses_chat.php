<?php
require_once '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['pesan'])) {
    $pesan = trim($_POST['pesan']);
    
    // Simpan sebagai 'pasien'
    $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('pasien', ?)");
    $stmt->execute([$pesan]);
    
    echo json_encode(['status' => 'success']);
}