<?php
require_once __DIR__ . '/../../includes/connection.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $stmt = $conn->prepare("UPDATE antrean SET status = ? WHERE id = ?");
    if($stmt->execute([$_POST['status'], $_POST['id']])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}