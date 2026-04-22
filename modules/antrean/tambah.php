<?php 
include '../../includes/header.php'; 

$show_modal = false;
$no_urut = "";

if(isset($_POST['submit'])){
    try {
        $alamat_lengkap = "Desa/Kel. " . $_POST['desa'] . ", Kota/Kab. " . $_POST['kota'];

        // 1. Simpan ke tabel pasien
        $s1 = $conn->prepare("INSERT INTO pasien (nama_pasien, no_hp, alamat) VALUES (?,?,?)");
        $s1->execute([$_POST['nama'], $_POST['hp'], $alamat_lengkap]);
        $id_p = $conn->lastInsertId();

        // 2. Ambil Nama Poli & Nomor Antrean Terakhir
        // Kita butuh nama_poli buat dimasukin ke tabel antrean biar dashboard admin gak kosong
        $s_poli = $conn->prepare("SELECT nama_poli FROM poli WHERE id_poli = ?");
        $s_poli->execute([$_POST['poli']]);
        $data_poli = $s_poli->fetch(PDO::FETCH_ASSOC);
        $nama_poli_pilihan = $data_poli['nama_poli'];

        $s2 = $conn->prepare("SELECT MAX(no_urut) AS terakhir FROM antrean WHERE id_poli = ?");
        $s2->execute([$_POST['poli']]);
        $row = $s2->fetch(PDO::FETCH_ASSOC);
        
        $no_urut = ($row['terakhir'] != null) ? $row['terakhir'] + 1 : 1;

        // 3. Masukkan ke tabel antrean (LENGKAPI SEMUA KOLOM)
        // Kita tambahin nama_pasien, nama_poli, status, dan waktu biar dashboard admin "paham"
        $s3 = $conn->prepare("INSERT INTO antrean (id_pasien, nama_pasien, nama_poli, no_urut, status, waktu) VALUES (?,?,?,?,?, NOW())");
        $s3->execute([
            $id_p, 
            $_POST['nama'], 
            $nama_poli_pilihan, 
            $no_urut, 
            'Menunggu' // Status awal wajib 'Menunggu' biar masuk kolom kiri
        ]);

        $show_modal = true; 
    } catch(Exception $e) { 
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>"; 
    }
}
?>

<style>
    /* Modal Styling */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center; z-index: 9999; }
    .modal-box { background: white; padding: 40px; border-radius: 20px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .check-icon { font-size: 60px; color: #22c55e; margin-bottom: 20px; }
    .nomor-antrean { font-size: 48px; font-weight: 800; color: #0f52ba; margin: 20px 0; display: block; }
    .btn-selesai { display: inline-block; padding: 12px 30px; background: #0f52ba; color: white; text-decoration: none; border-radius: 10px; font-weight: 600; }
    
    /* Layout Styling */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .alamat-box { background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #475569; font-size: 0.95rem;}
    .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; box-sizing: border-box; background: white; font-family: inherit;}
    .form-group input:focus, .form-group select:focus { outline: none; border-color: #0f52ba; box-shadow: 0 0 0 3px rgba(15, 82, 186, 0.1); }
</style>

<?php if($show_modal): ?>
<div class="modal-overlay">
    <div class="modal-box">
        <div class="check-icon">✓</div>
        <h2 style="margin:0;">Pendaftaran Berhasil!</h2>
        <p style="color: #64748b; margin-top: 10px;">Nomor Antrean Anda:</p>
        <span class="nomor-antrean"><?php echo $no_urut; ?></span>
        <a href="../../dashboard.php" class="btn-selesai">Selesai</a>
    </div>
</div>
<?php endif; ?>

<div class="container" style="max-width: 650px; margin: 40px auto; padding: 0 20px;">
    <div style="margin-bottom: 30px; text-align: center;">
        <h2>Pendaftaran Pasien</h2>
        <p style="color: #64748b;">Silakan lengkapi form di bawah ini.</p>
    </div>

    <div class="card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <form method="POST">
            
            <div class="grid-2" style="margin-bottom: 25px;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" placeholder="" required>
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="int" name="hp" placeholder="" required>
                </div>
            </div>

            <div class="alamat-box">
                <h4 style="margin-top:0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; color: #334155; margin-bottom: 15px;">Alamat Domisili</h4>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label>Kota/Kabupaten</label>
                        <input type="text" name="kota" placeholder="Contoh : Cianjur" required>
                    </div>
                    <div class="form-group">
                        <label>Desa</label>
                        <input type="text" name="desa" placeholder="Contoh : Selajambe" required>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label>Pilih Poli Tujuan</label>
                <select name="poli" required style="border-color: #0f52ba; border-width: 2px; font-weight: bold; color: #0f52ba;">
                    <option value="" disabled selected>-- Silakan Pilih Poli --</option>
                    <?php 
                    $list = $conn->query("SELECT * FROM poli");
                    foreach($list as $p) echo "<option value='{$p['id_poli']}'>{$p['nama_poli']}</option>"; 
                    ?>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; border:none; border-radius:8px; background: #0f52ba; color:white; cursor:pointer; font-weight:bold; margin-bottom: 10px;">Ambil Antrean Sekarang</button>
            <a href="../../dashboard.php" class="btn btn-outline" style="display:block; text-align:center; padding: 15px; border-radius:8px; color: #64748b; text-decoration:none; background:#f1f5f9; font-weight:bold;">Batal & Kembali</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>