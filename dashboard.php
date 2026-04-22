<?php include 'includes/header.php'; ?>

<div class="hero" style="text-align: center; margin-bottom: 40px;">
    <h1>Halo! 👋</h1>
    <p>Selamat datang di Klinik Sehat Digital. Ada yang bisa kami bantu hari ini?</p>
</div>

<div class="grid-menu" style="display: flex; gap: 20px; justify-content: center; max-width: 1000px; margin: 0 auto; align-items: stretch;">
    
    <div class="card" style="flex: 1; display: flex; flex-direction: column; text-align: center; padding: 30px; border: 1px solid #eaeaea; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="font-size: 3rem; margin-bottom: 15px;">🏥</div>
        <h3>Daftar Antrean</h3>
        <p style="color: #666; margin-bottom: 25px;">Malas antre di lokasi? Daftar dari rumah sekarang dan datang saat nomor Anda hampir dipanggil.</p>
        <a href="modules/antrean/tambah.php" class="btn btn-primary" style="margin-top: auto; display: block; background: #0f52ba; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Ambil Antrean</a>
    </div>

    <div class="card" style="flex: 1; display: flex; flex-direction: column; text-align: center; padding: 30px; border: 1px solid #eaeaea; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="font-size: 3rem; margin-bottom: 15px;">💬</div>
        <h3>Konsultasi Dokter</h3>
        <p style="color: #666; margin-bottom: 25px;">Punya keluhan ringan atau ingin tanya seputar kesehatan? Konsultasi langsung secara online dengan dokter kami.</p>
        <a href="konsultasi/index.php" class="btn btn-primary" style="margin-top: auto; display: block; background: #0f52ba; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Mulai Konsultasi</a>
    </div>

</div>
</div>

<div style="max-width: 1000px; margin: 50px auto 0; background: #0f52ba; color: white; padding: 30px; border-radius: 20px; text-align: center;">
    <h3 style="margin-top: 0;">Daftar Antrian</h3>
    <p style="opacity: 0.9; margin-bottom: 20px;">Lihat antrian di bagian sini, terima kasih sudah menunggu.</p>
    <a href="modules/poli/index.php" class="btn" style="display: inline-block; background: white; color: #0f52ba; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">Lihat Antrian</a>
</div>

<?php include 'includes/footer.php'; ?>