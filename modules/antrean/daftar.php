<?php 
require_once __DIR__ . '/../../includes/connection.php'; 

if (isset($_POST['daftar'])) {
    $nama = $_POST['nama_pasien'];
    $poli = $_POST['poli'];
    $status = 'Menunggu'; // Status awal selalu menunggu

    // Masukin ke database
    $sql = "INSERT INTO antrean (nama_pasien, nama_poli, status, waktu) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nama, $poli, $status]);

    echo "<script>alert('Berhasil daftar! Silakan tunggu dipanggil.'); window.location='daftar.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Pasien</title>
    <style>
        body { font-family: sans-serif; background: #f4f7fa; display: flex; justify-content: center; padding-top: 50px; }
        .box-daftar { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #0f52ba; text-align: center; }
        input, select, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #0f52ba; color: white; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="box-daftar">
    <h2>Daftar Antrean</h2>
    <form method="POST">
        <input type="text" name="nama_pasien" placeholder="Masukkan Nama Lengkap" required>
        <select name="poli" required>
            <option value="">-- Pilih Poli --</option>
            <option value="Umum">Poli Umum</option>
            <option value="Gigi">Poli Gigi</option>
            <option value="Anak">Poli Anak</option>
        </select>
        <button type="submit" name="daftar">DAFTAR SEKARANG</button>
    </form>
</div>

</body>
</html>