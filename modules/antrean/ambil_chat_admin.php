<?php
// Jangan lupa panggil koneksi
require_once __DIR__ . '/../../includes/connection.php';

$chats = $conn->query("SELECT * FROM chat_konsultasi ORDER BY waktu ASC");

// Ini loop yang tadi bocor, di sini harus ditutup pake endwhile!
while($c = $chats->fetch()) : 
    $isAdmin = ($c['pengirim'] == 'admin');
?>
    <div class="bubble <?= $isAdmin ? 'bubble-admin' : 'bubble-other' ?>">
        <small style="display:block; font-size:0.7rem; font-weight:bold; margin-bottom:3px; opacity:0.8;">
            <?= strtoupper($c['pengirim']) ?> • <?= date('H:i', strtotime($c['waktu'])) ?>
        </small>
        <?= htmlspecialchars($c['pesan']) ?>
    </div>
<?php endwhile; ?>