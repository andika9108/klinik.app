-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 22 Apr 2026 pada 16.02
-- Versi Server: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_klinik_antrean`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `antrean`
--

CREATE TABLE `antrean` (
  `id_antrean` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  `no_urut` int(11) DEFAULT NULL,
  `status` enum('Menunggu','Proses','Selesai') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `antrean`
--

INSERT INTO `antrean` (`id_antrean`, `id_pasien`, `id_poli`, `no_urut`, `status`) VALUES
(1, 1, 1, 1, ''),
(2, 2, 1, 2, 'Menunggu'),
(3, 3, 3, 1, 'Menunggu'),
(4, 4, 1, 3, 'Menunggu'),
(5, 5, 3, 2, 'Menunggu'),
(6, 6, 2, 1, 'Menunggu'),
(7, 7, 2, 2, 'Menunggu'),
(8, 8, 2, 3, 'Menunggu'),
(9, 9, 2, 4, 'Menunggu'),
(10, 10, 3, 3, 'Menunggu'),
(11, 11, 2, 5, 'Menunggu'),
(12, 12, 2, 6, 'Menunggu'),
(13, 13, 1, 4, 'Menunggu'),
(14, 14, 1, 5, 'Menunggu'),
(15, 15, 1, 6, 'Menunggu'),
(16, 16, 3, 4, 'Menunggu'),
(17, 17, 1, 7, 'Menunggu'),
(18, 18, 1, 8, 'Menunggu'),
(19, 19, 2, 7, 'Menunggu'),
(20, 20, 1, 9, 'Menunggu'),
(21, 24, 2, 8, 'Menunggu'),
(22, 25, 2, 9, 'Menunggu'),
(23, 26, 3, 5, 'Menunggu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_konsultasi`
--

CREATE TABLE `chat_konsultasi` (
  `id_chat` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `pengirim` enum('pasien','ai','admin') NOT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `chat_konsultasi`
--

INSERT INTO `chat_konsultasi` (`id_chat`, `id_pasien`, `pengirim`, `pesan`, `waktu`) VALUES
(1, NULL, 'admin', 'hai', '2026-04-22 10:17:36'),
(2, NULL, 'admin', 'popok', '2026-04-22 10:18:50'),
(3, NULL, 'admin', 'popok', '2026-04-22 10:22:37'),
(4, NULL, 'pasien', 'p', '2026-04-22 10:33:06'),
(5, NULL, 'ai', 'Maaf, AI sedang istirahat sebentar. Coba tanya lagi ya!', '2026-04-22 10:33:06'),
(6, NULL, 'pasien', 'halo', '2026-04-22 10:33:09'),
(7, NULL, 'ai', 'Maaf, AI sedang istirahat sebentar. Coba tanya lagi ya!', '2026-04-22 10:33:10'),
(8, NULL, 'pasien', 'p', '2026-04-22 10:34:36'),
(9, NULL, 'ai', 'Google Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:34:36'),
(10, NULL, 'pasien', 'p', '2026-04-22 10:35:30'),
(11, NULL, 'ai', 'Aduh, Google bilang: models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:35:30'),
(12, NULL, 'pasien', 'halo', '2026-04-22 10:35:34'),
(13, NULL, 'ai', 'Aduh, Google bilang: models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:35:35'),
(14, NULL, 'pasien', 'halo', '2026-04-22 10:38:37'),
(15, NULL, 'ai', 'Ada kendala teknis: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:38:37'),
(16, NULL, 'pasien', 'p', '2026-04-22 10:39:33'),
(17, NULL, 'ai', 'Google Error (404): models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:39:33'),
(18, NULL, 'pasien', 'p', '2026-04-22 10:41:53'),
(19, NULL, 'ai', 'Google Error (404): models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:41:53'),
(20, NULL, 'pasien', 'p', '2026-04-22 10:43:49'),
(21, NULL, 'ai', 'Google Error (404): models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:43:49'),
(22, NULL, 'pasien', 'p', '2026-04-22 10:49:34'),
(23, NULL, 'ai', 'Google Error: models/gemini-pro is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:49:36'),
(24, NULL, 'pasien', 'halo', '2026-04-22 10:49:39'),
(25, NULL, 'ai', 'Google Error: models/gemini-pro is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:49:40'),
(26, NULL, 'pasien', 'saya pusing', '2026-04-22 10:52:46'),
(27, NULL, 'ai', 'Google Error: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:52:46'),
(28, NULL, 'pasien', 'wou\\y', '2026-04-22 10:55:07'),
(29, NULL, 'ai', 'Siklus Google lagi sibuk. Coba tanya sekali lagi ya!', '2026-04-22 10:55:07'),
(30, NULL, 'pasien', 'halo', '2026-04-22 10:55:10'),
(31, NULL, 'ai', 'Siklus Google lagi sibuk. Coba tanya sekali lagi ya!', '2026-04-22 10:55:10'),
(32, NULL, 'pasien', 'kalaoala', '2026-04-22 10:55:12'),
(33, NULL, 'ai', 'Siklus Google lagi sibuk. Coba tanya sekali lagi ya!', '2026-04-22 10:55:13'),
(34, NULL, 'pasien', 'saya sakit', '2026-04-22 10:55:38'),
(35, NULL, 'ai', 'Siklus Google lagi sibuk. Coba tanya sekali lagi ya!', '2026-04-22 10:55:39'),
(36, NULL, 'pasien', 'halo', '2026-04-22 10:56:55'),
(37, NULL, 'ai', 'Info: models/gemini-1.5-flash is not found for API version v1, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:56:55'),
(38, NULL, 'pasien', 'HALO', '2026-04-22 10:57:51'),
(39, NULL, 'ai', 'Info Google: API key expired. Please renew the API key.', '2026-04-22 10:57:51'),
(40, NULL, 'pasien', 'HALO', '2026-04-22 10:58:59'),
(41, NULL, 'ai', 'Info Google: models/gemini-1.5-flash is not found for API version v1beta, or is not supported for generateContent. Call ListModels to see the list of available models and their supported methods.', '2026-04-22 10:58:59'),
(42, NULL, 'pasien', 'halo', '2026-04-22 11:00:53'),
(43, NULL, 'admin', 'halo juga, ada yang bisa saya bantu?', '2026-04-22 11:01:11'),
(44, NULL, 'pasien', 'iya dok jadi anak saya sakit ini nih', '2026-04-22 11:03:27'),
(45, NULL, 'admin', 'ppp', '2026-04-22 11:40:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nama_pasien` varchar(150) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nama_pasien`, `no_hp`, `alamat`) VALUES
(1, 'dika', '083820294192', 'kp buud'),
(2, 'ete', '083820294192', 'w'),
(3, 'dv', '083820294192', 'a'),
(4, 'a', 'a', 'a'),
(5, 'QWRWRE', '083820294192', '<?php \r\ninclude \'../../includes/header.php\'; \r\n\r\n// Logika pendaftaran\r\n$show_modal = false;\r\n$no_urut = \"\";\r\n\r\nif(isset($_POST[\'submit\'])){\r\n    try {\r\n        $s1 = $conn->prepare(\"INSERT INTO pasien (nama_pasien, no_hp, alamat) VALUES (?,?,?)\");\r\n        $s1->execute([$_POST[\'nama\'], $_POST[\'hp\'], $_POST[\'alamat\']]);\r\n        $id_p = $conn->lastInsertId();\r\n\r\n        $s2 = $conn->prepare(\"SELECT COUNT(*) FROM antrean WHERE id_poli = ?\");\r\n        $s2->execute([$_POST[\'poli\']]);\r\n        $no_urut = $s2->fetchColumn() + 1;\r\n\r\n        $s3 = $conn->prepare(\"INSERT INTO antrean (id_pasien, id_poli, no_urut) VALUES (?,?,?)\");\r\n        $s3->execute([$id_p, $_POST[\'poli\'], $no_urut]);\r\n\r\n        $show_modal = true; // Aktifkan modal sukses\r\n    } catch(Exception $e) { \r\n        echo \"<script>alert(\'Terjadi kesalahan!\');</script>\"; \r\n    }\r\n}\r\n?>\r\n\r\n<style>\r\n    /* Modal Styling - Tepat di Tengah */\r\n    .modal-overlay {\r\n        position: fixed; top: 0; left: 0; width: 100%; height: 100%;\r\n        background: rgba(0,0,0,0.6); display: flex; justify-content: center;\r\n        align-items: center; z-index: 9999;\r\n    }\r\n    .modal-box {\r\n        background: white; padding: 40px; border-radius: 20px;\r\n        text-align: center; max-width: 400px; width: 90%;\r\n        box-shadow: 0 10px 25px rgba(0,0,0,0.2);\r\n    }\r\n    .check-icon {\r\n        font-size: 60px; color: #22c55e; margin-bottom: 20px;\r\n    }\r\n    .nomor-antrean {\r\n        font-size: 48px; font-weight: 800; color: #0f52ba;\r\n        margin: 20px 0; display: block;\r\n    }\r\n    .btn-selesai {\r\n        display: inline-block; padding: 12px 30px; background: #0f52ba;\r\n        color: white; text-decoration: none; border-radius: 10px; font-weight: 600;\r\n    }\r\n</style>\r\n\r\n<?php if($show_modal): ?>\r\n<div class=\"modal-overlay\">\r\n    <div class=\"modal-box\">\r\n        <div class=\"check-icon\">âœ“</div>\r\n        <h2 style=\"margin:0;\">Pendaftaran Berhasil!</h2>\r\n        <p style=\"color: #64748b; margin-top: 10px;\">Nomor Antrean Anda:</p>\r\n        <span class=\"nomor-antrean\"><?php echo $no_urut; ?></span>\r\n        <p style=\"color: #64748b; margin-bottom: 25px; font-size: 14px;\">Silakan datang sesuai urutan. Simpan nomor ini.</p>\r\n        <a href=\"../../dashboard.php\" class=\"btn-selesai\">Selesai</a>\r\n    </div>\r\n</div>\r\n<?php endif; ?>\r\n\r\n<div class=\"container\">\r\n    <div style=\"margin-bottom: 30px; text-align: center;\">\r\n        <h2>Pendaftaran Pasien</h2>\r\n        <p style=\"color: #64748b;\">Isi formulir dengan benar.</p>\r\n    </div>\r\n\r\n    <div class=\"form-container\">\r\n        <div class=\"card\">\r\n            <form method=\"POST\">\r\n                <div class=\"form-group\">\r\n                    <label>Nama Lengkap</label>\r\n                    <input type=\"text\" name=\"nama\" required>\r\n                </div>\r\n\r\n                <div class=\"form-group\">\r\n                    <label>Nomor WhatsApp</label>\r\n                    <input type=\"text\" name=\"hp\" required>\r\n                </div>\r\n\r\n                <div class=\"form-group\">\r\n                    <label>Alamat</label>\r\n                    <textarea name=\"alamat\" rows=\"3\" required></textarea>\r\n                </div>\r\n\r\n                <div class=\"form-group\">\r\n                    <label>Pilih Poli</label>\r\n                    <select name=\"poli\" required>\r\n                        <option value=\"\" disabled selected></option>\r\n                        <?php \r\n                        $list = $conn->query(\"SELECT * FROM poli\");\r\n                        foreach($list as $p) echo \"<option value=\'{$p[\'id_poli\']}\'>{$p[\'nama_poli\']}</option>\"; \r\n                        ?>\r\n                    </select>\r\n                </div>\r\n\r\n                <button type=\"submit\" name=\"submit\" class=\"btn btn-primary\">Daftar Sekarang</button>\r\n                <a href=\"../../dashboard.php\" class=\"btn btn-outline\">Batal & Kembali</a>\r\n            </form>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<?php include \'../../includes/footer.php\'; ?>'),
(6, 'QWRWRE', '083820294192', ';l'),
(7, 'dv', '083820294192', '\''),
(8, 'ete', 'a', 'za'),
(9, 'QWRWRE', 'a', 'z'),
(10, 'd', '083820294192', 'z'),
(11, 'dv', 's', 's'),
(12, 'sad', 's', 's'),
(13, 'shibal', 's', 's'),
(14, 'sad', 's', 's'),
(15, 'aa', 'xax', 'aas'),
(16, 'sipa', '083820294192', 'kp buud'),
(17, 'popoa', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(18, 'ete', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(19, 'ete', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(20, 'dv', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(21, 'ete', 's', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(22, 'dv', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(23, 'dv', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(24, 'dv', '083820294192', 'Desa/Kel. hegar, Kota/Kab. cianjur'),
(25, 'a', 'a', 'Desa/Kel. hegar, Kota/Kab. a'),
(26, 's', 'a', 'Desa/Kel. a, Kota/Kab. a');

-- --------------------------------------------------------

--
-- Struktur dari tabel `poli`
--

CREATE TABLE `poli` (
  `id_poli` int(11) NOT NULL,
  `nama_poli` varchar(100) DEFAULT NULL,
  `kuota_maks` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `poli`
--

INSERT INTO `poli` (`id_poli`, `nama_poli`, `kuota_maks`) VALUES
(1, 'Poli Gigi', 20),
(2, 'Poli Umum', 50),
(3, 'Poli Anak', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `antrean`
--
ALTER TABLE `antrean`
  ADD PRIMARY KEY (`id_antrean`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_poli` (`id_poli`);

--
-- Indexes for table `chat_konsultasi`
--
ALTER TABLE `chat_konsultasi`
  ADD PRIMARY KEY (`id_chat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `poli`
--
ALTER TABLE `poli`
  ADD PRIMARY KEY (`id_poli`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `antrean`
--
ALTER TABLE `antrean`
  MODIFY `id_antrean` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `chat_konsultasi`
--
ALTER TABLE `chat_konsultasi`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `poli`
--
ALTER TABLE `poli`
  MODIFY `id_poli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `antrean`
--
ALTER TABLE `antrean`
  ADD CONSTRAINT `antrean_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE,
  ADD CONSTRAINT `antrean_ibfk_2` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id_poli`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
