<?php include '../../includes/header.php'; ?>
<h2>Daftar Poli & Kuota</h2>
<table>
    <thead>
        <tr><th>ID</th><th>Nama Poli</th><th>Kuota Maksimal</th></tr>
    </thead>
    <tbody>
        <?php
        $data = $conn->query("SELECT * FROM poli")->fetchAll();
        foreach($data as $p) {
            echo "<tr><td>{$p['id_poli']}</td><td>{$p['nama_poli']}</td><td>{$p['kuota_maks']} Pasien</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php include '../../includes/footer.php'; ?>