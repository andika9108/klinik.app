<?php include 'includes/header.php'; ?>

<div class="hero">
    <h1>Halo! 👋</h1>
    <p>Selamat datang di Klinik Sehat Digital. Ada yang bisa kami bantu hari ini?</p>
</div>

<div class="grid-menu">
    <div class="card">
        <div style="font-size: 3rem; margin-bottom: 15px;">🏥</div>
        <h3>Daftar Antrean</h3>
        <p>Malas antre di lokasi? Daftar dari rumah sekarang dan datang saat nomor Anda hampir dipanggil.</p>
        <a href="modules/antrean/tambah.php" class="btn btn-primary">Ambil Antrean</a>
    </div>

    <div class="card">
        <div style="font-size: 3rem; margin-bottom: 15px;">👨‍⚕️</div>
        <h3>Ruang Admin</h3>
        <p>Halaman khusus staf resepsionis untuk memanggil pasien dan mengelola urutan antrean poli.</p>
        <a href="modules/antrean/index.php" class="btn btn-outline">Masuk Ruang Admin</a>
    </div>
</div>

<div style="margin-top: 50px; background: #0f52ba; color: white; padding: 30px; border-radius: 20px; text-align: center;">
    <h3 style="margin-top: 0;">Cek Kuota Poli</h3>
    <p style="opacity: 0.9;">Lihat ketersediaan kuota harian untuk setiap poli spesialis kami.</p>
    <a href="modules/poli/index.php" class="btn" style="background: white; color: #0f52ba;">Lihat Data Poli</a>
</div>

<?php include 'includes/footer.php'; ?>