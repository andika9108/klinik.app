<?php 
include '../../includes/header.php'; 

$show_modal = false;
$no_urut = "";

if(isset($_POST['submit'])){
    try {
        // 1. Simpan data pasien dulu
        $s1 = $conn->prepare("INSERT INTO pasien (nama_pasien, no_hp, alamat) VALUES (?,?,?)");
        $s1->execute([$_POST['nama'], $_POST['hp'], $_POST['alamat']]);
        $id_p = $conn->lastInsertId();

        // 2. AMBIL NOMOR TERAKHIR di Poli yang dipilih (Biar Gak Random)
        $s2 = $conn->prepare("SELECT MAX(no_urut) AS terakhir FROM antrean WHERE id_poli = ?");
        $s2->execute([$_POST['poli']]);
        $row = $s2->fetch(PDO::FETCH_ASSOC);
        
        // Kalau belum ada antrean di poli itu, mulai dari 1. Kalau ada, tambah 1.
        $no_urut = ($row['terakhir'] != null) ? $row['terakhir'] + 1 : 1;

        // 3. Masukkan ke tabel antrean dengan nomor urut yang benar
        $s3 = $conn->prepare("INSERT INTO antrean (id_pasien, id_poli, no_urut) VALUES (?,?,?)");
        $s3->execute([$id_p, $_POST['poli'], $no_urut]);

        $show_modal = true; 
    } catch(Exception $e) { 
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>"; 
    }
}
?>

<style>
    /* Modal Styling - Tepat di Tengah */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; justify-content: center;
        align-items: center; z-index: 9999;
    }
    .modal-box {
        background: white; padding: 40px; border-radius: 20px;
        text-align: center; max-width: 400px; width: 90%;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .check-icon {
        font-size: 60px; color: #22c55e; margin-bottom: 20px;
    }
    .nomor-antrean {
        font-size: 48px; font-weight: 800; color: #0f52ba;
        margin: 20px 0; display: block;
    }
    .btn-selesai {
        display: inline-block; padding: 12px 30px; background: #0f52ba;
        color: white; text-decoration: none; border-radius: 10px; font-weight: 600;
    }
</style>

<?php if($show_modal): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <div class="check-icon">✓</div>
        <h2 style="margin:0;">Pendaftaran Berhasil!</h2>
        <p style="color: #64748b; margin-top: 10px;">Nomor Antrean Anda:</p>
        <span class="nomor-antrean"><?php echo $no_urut; ?></span>
        <p style="color: #64748b; margin-bottom: 25px; font-size: 14px;">Silakan datang sesuai urutan. Simpan nomor ini.</p>
        <a href="../../dashboard.php" class="btn-selesai">Selesai</a>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <div style="margin-bottom: 30px; text-align: center;">
        <h2>Pendaftaran Pasien</h2>
        <p style="color: #64748b;">Isi formulir dengan benar.</p>
    </div>

    <div class="form-container">
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="hp" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label>Pilih Poli</label>
                    <select name="poli" required>
                        <option value="" disabled selected></option>
                        <?php 
                        $list = $conn->query("SELECT * FROM poli");
                        foreach($list as $p) echo "<option value='{$p['id_poli']}'>{$p['nama_poli']}</option>"; 
                        ?>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Daftar Sekarang</button>
                <a href="../../dashboard.php" class="btn btn-outline">Batal & Kembali</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>