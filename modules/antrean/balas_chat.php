<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/connection.php'; 

if (isset($_SESSION['admin_klinik']) && !empty($_POST['pesan_admin'])) {
    $pesan = trim($_POST['pesan_admin']);
    try {
        $stmt = $conn->prepare("INSERT INTO chat_konsultasi (pengirim, pesan) VALUES ('admin', ?)");
        $stmt->execute([$pesan]);
        header("Location: index.php");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}