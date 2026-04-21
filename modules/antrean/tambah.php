<?php 
include '../../includes/header.php'; 
if(isset($_POST['submit'])){
    // Simpan Pasien
    $s1 = $conn->prepare("INSERT INTO pasien (nama_pasien, no_hp, alamat) VALUES (?,?,?)");
    $s1->execute([$_POST['nama'], $_POST['hp'], $_POST['alamat']]);
    $id_p = $conn->lastInsertId();

    // Hitung No Urut
    $s2 = $conn->prepare("SELECT COUNT(*) FROM antrean WHERE id_poli = ?");
    $s2->execute([$_POST['poli']]);
    $no = $s2->fetchColumn() + 1;

    // Simpan Antrean
    $s3 = $conn->prepare("INSERT INTO antrean (id_pasien, id_poli, no_urut) VALUES (?,?,?)");
    $s3->execute([$id_p, $_POST['poli'], $no]);
    echo "<script>alert('Berhasil! Nomor Antrean Anda: $no'); window.location='../../dashboard.php';</script>";
}
?>
<h2>Pendaftaran Antrean</h2>
<form method="POST">
    <label>Nama Pasien</label><input type="text" name="nama" required>
    <label>No HP</label><input type="text" name="hp" required>
    <label>Alamat</label><textarea name="alamat"></textarea>
    <label>Pilih Poli</label>
    <select name="poli">
        <?php foreach($conn->query("SELECT * FROM poli") as $p) echo "<option value='{$p['id_poli']}'>{$p['nama_poli']}</option>"; ?>
    </select>
    <button type="submit" name="submit" class="btn btn-blue" style="margin-top:10px;">Daftar Sekarang</button>
</form>
<?php include '../../includes/footer.php'; ?>