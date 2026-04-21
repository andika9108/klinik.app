<?php
$host = "localhost";
$db   = "db_klinik_antrean";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (session_status() === PHP_SESSION_NONE) session_start();
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>