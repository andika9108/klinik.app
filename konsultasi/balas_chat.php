<?php
require_once '../../includes/connection.php';

if (isset($_POST['pesan_admin'])) {
    $pesan = trim($_POST['pesan_admin']);
    
    // Simpan sebagai 'admin'
    $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
    $stmt->execute([$pesan]);
    
    header("Location: index.php"); // Balik ke dashboard admin
}