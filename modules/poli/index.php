<?php include '../../includes/header.php'; ?>

<style>
    /* Styling tambahan agar tabel terlihat rapi dan modern */
    .queue-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .queue-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .queue-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
    }
    .poli-card {
        background: white;
        border: 1px solid #eaeaea;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .poli-title {
        background: #0f52ba;
        color: white;
        padding: 15px 20px;
        margin: 0;
        font-size: 1.2rem;
        text-align: center;
    }
    .styled-table {
        width: 100%;
        border-collapse: collapse;
    }
    .styled-table th, .styled-table td {
        padding: 12px 15px;
        text-align: left;
    }
    .styled-table th {
        background-color: #f4f6f8;
        color: #333;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
    }
    .styled-table td {
        border-bottom: 1px solid #eee;
        color: #555;
    }
    .styled-table tbody tr:hover {
        background-color: #f9f9f9;
    }
    .no-data {
        text-align: center;
        padding: 20px;
        color: #999;
        font-style: italic;
    }
    .badge-no {
        background: #e0e7ff;
        color: #0f52ba;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: bold;
    }
</style>

<div class="queue-container">
    <div class="queue-header">
        <h2 style="color: #0f52ba; font-size: 2rem; margin-bottom: 10px;">Monitor Antrean Hari Ini</h2>
        <p style="color: #666;">Menampilkan daftar pasien yang sedang menunggu di masing-masing poli.</p>
    </div>

    <div class="queue-grid">
        <?php
        // 1. Ambil 3 data poli dari database (Misal: Umum, Gigi, Anak)
        $data_poli = $conn->query("SELECT * FROM poli LIMIT 3")->fetchAll();

        // 2. Lakukan perulangan untuk membuat 3 kotak tabel
        foreach($data_poli as $poli) {
            $id_poli = $poli['id_poli'];
            $nama_poli = $poli['nama_poli'];
        ?>
            
            <div class="poli-card">
                <h3 class="poli-title">🩺 <?= htmlspecialchars($nama_poli) ?></h3>
                
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th width="30%">Nomor</th>
                            <th>Nama Pasien</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 3. Ambil data pasien dengan perintah JOIN sesuai struktur database Anda
                        $query_antrean = "
                            SELECT antrean.no_urut, pasien.nama_pasien 
                            FROM antrean 
                            JOIN pasien ON antrean.id_pasien = pasien.id_pasien 
                            WHERE antrean.id_poli = '$id_poli' 
                            ORDER BY antrean.no_urut ASC
                        ";
                        
                        // Eksekusi query
                        $data_antrean = @$conn->query($query_antrean)->fetchAll();

                        if($data_antrean && count($data_antrean) > 0) {
                            foreach($data_antrean as $antre) {
                                echo "<tr>";
                                echo "<td><span class='badge-no'>#" . htmlspecialchars($antre['no_urut']) . "</span></td>";
                                echo "<td>" . htmlspecialchars($antre['nama_pasien']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            // Jika tidak ada yang mengantre di poli ini
                            echo "<tr><td colspan='2'><div class='no-data'>Belum ada antrean</div></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        <?php } ?>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="../../dashboard.php" style="color: #666; text-decoration: none; font-weight: bold;">&larr; Kembali ke Beranda</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>