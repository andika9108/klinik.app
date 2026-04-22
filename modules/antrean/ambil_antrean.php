<?php
require_once __DIR__ . '/../../includes/connection.php';

$q = $conn->query("SELECT * FROM antrean ORDER BY id ASC");
$no = 1;

while($r = $q->fetch()) {
    $bg = "#94a3b8"; // Menunggu
    if($r['status'] == 'Dipanggil') $bg = "#f59e0b";
    if($r['status'] == 'Selesai') $bg = "#10b981";

    echo '<tr>';
    echo '<td>'.$no++.'</td>';
    echo '<td style="font-weight:600; color:#334155;">'.htmlspecialchars($r['nama_pasien']).'</td>';
    echo '<td><span class="badge" style="background:'.$bg.'">'.$r['status'].'</span></td>';
    echo '<td style="text-align:right;">';
        if($r['status'] == 'Menunggu') {
            echo '<button class="btn-action" style="background:#0f52ba" onclick="gantiStatus('.$r['id'].', \'Dipanggil\')">Panggil</button>';
        } elseif($r['status'] == 'Dipanggil') {
            echo '<button class="btn-action" style="background:#10b981" onclick="gantiStatus('.$r['id'].', \'Selesai\')">Selesai</button>';
        } else {
            echo '<span style="color:#cbd5e1; font-size:0.8rem">Selesai ✓</span>';
        }
    echo '</td>';
    echo '</tr>';
}