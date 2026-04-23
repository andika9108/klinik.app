<?php
// Cukup naik SATU tingkat saja
require_once '../includes/connection.php';

// Pastikan pake id_chat sesuai struktur tabel lu
$q = $conn->query("SELECT * FROM chat_konsultasi ORDER BY id_chat ASC");

while ($r = $q->fetch()) {
    $class = ($r['pengirim'] == 'pasien') ? 'msg-user' : 'msg-admin';
    echo '<div class="message ' . $class . '">';
    echo htmlspecialchars($r['pesan']);
    echo '</div>';
}
?>