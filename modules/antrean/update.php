<?php
require_once '../../includes/connection.php';
$stmt = $conn->prepare("UPDATE antrean SET status = ? WHERE id_antrean = ?");
$stmt->execute([$_GET['s'], $_GET['id']]);
header("Location: index.php");