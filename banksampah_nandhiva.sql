-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Sep 2026 pada 06.14
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksampah_nandhiva`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `sampah`
--

CREATE TABLE `sampah` (
  `id_sampah` int(11) NOT NULL,
  `jenis_sampah` varchar(50) NOT NULL,
  `harga_per_kg` decimal(10,2) NOT NULL,
  `stok_kg` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sampah`
--

INSERT INTO `sampah` (`id_sampah`, `jenis_sampah`, `harga_per_kg`, `stok_kg`) VALUES
(1, 'Plastik', 3000.00, 0.00),
(2, 'Kertas', 2000.00, 0.00),
(3, 'Kardus', 2500.00, 0.00),
(4, 'Botol Plastik', 4000.00, 0.00),
(5, 'Kaleng', 5000.00, 0.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `setoran_pengepul`
--

CREATE TABLE `setoran_pengepul` (
  `id_setor` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `berat_kg` decimal(10,2) NOT NULL,
  `harga_jual_per_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(12,2) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tarik_saldo`
--

CREATE TABLE `tarik_saldo` (
  `id_tarik` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `status` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'pending',
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tarik_saldo`
--

INSERT INTO `tarik_saldo` (`id_tarik`, `id_user`, `nominal`, `status`, `tanggal`) VALUES
(1, 2, 10000.00, 'pending', '2026-09-23 09:04:29'),
(2, 2, 24000.00, 'pending', '2026-09-23 09:14:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `tipe` enum('setor','ambil') NOT NULL DEFAULT 'setor',
  `berat_kg` decimal(10,2) NOT NULL,
  `total_rp` decimal(10,2) NOT NULL,
  `status` enum('pending','diverifikasi','ditolak') NOT NULL DEFAULT 'pending',
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_sampah`, `tipe`, `berat_kg`, `total_rp`, `status`, `tanggal`) VALUES
(1, 2, 1, 'setor', 5.00, 15000.00, 'diverifikasi', '2026-09-23 09:04:28'),
(2, 2, 5, 'setor', 34.00, 170000.00, 'pending', '2026-09-23 09:13:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa','guru','pengepul') NOT NULL DEFAULT 'siswa',
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `role`, `saldo`) VALUES
(1, 'Administrator', 'admin', 'admin123', 'admin', 0.00),
(2, 'nandhiva', 'siswa', 'siswa123', 'siswa', 25000.00),
(3, 'Bapak Guru', 'guru', 'guru123', 'guru', 50000.00),
(4, 'Pengepul Bank Sampah', 'pengepul', 'pengepul123', 'pengepul', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `sampah`
--
ALTER TABLE `sampah`
  ADD PRIMARY KEY (`id_sampah`);

--
-- Indeks untuk tabel `setoran_pengepul`
--
ALTER TABLE `setoran_pengepul`
  ADD PRIMARY KEY (`id_setor`),
  ADD KEY `id_sampah` (`id_sampah`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indeks untuk tabel `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  ADD PRIMARY KEY (`id_tarik`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_sampah` (`id_sampah`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id_sampah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `setoran_pengepul`
--
ALTER TABLE `setoran_pengepul`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  MODIFY `id_tarik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `setoran_pengepul`
--
ALTER TABLE `setoran_pengepul`
  ADD CONSTRAINT `setoran_pengepul_ibfk_1` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `setoran_pengepul_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  ADD CONSTRAINT `tarik_saldo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE IF NOT EXISTS `penjualan_pengepul` (`id_penjualan` int NOT NULL AUTO_INCREMENT,`id_admin` int NOT NULL,`id_pengepul` int NOT NULL,`id_sampah` int NOT NULL,`berat_kg` decimal(10,2) NOT NULL,`harga_per_kg` decimal(12,2) NOT NULL,`total_rp` decimal(14,2) NOT NULL,`status` enum('pending','disetujui','ditolak','selesai') NOT NULL DEFAULT 'pending',`tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id_penjualan`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
