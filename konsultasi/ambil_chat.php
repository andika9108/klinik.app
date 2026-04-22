<?php
require_once '../includes/connection.php';

$query = $conn->query("SELECT * FROM chat_konsultasi ORDER BY waktu ASC");
while ($row = $query->fetch()) {
    $class = ($row['pengirim'] == 'admin') ? 'msg-admin' : 'msg-user';
    $sender = ($row['pengirim'] == 'admin') ? 'Admin Klinik' : 'Anda';
    
    echo '<div class="message ' . $class . '">';
    echo '<small style="font-weight:bold; font-size:0.7rem; display:block;">' . $sender . '</small>';
    echo htmlspecialchars($row['pesan']);
    echo '<span class="time">' . date('H:i', strtotime($row['waktu'])) . '</span>';
    echo '</div>';
}
?>