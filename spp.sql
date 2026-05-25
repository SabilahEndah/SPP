-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 10:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spp`
--

-- --------------------------------------------------------

--
-- Table structure for table `cek_pembayaran`
--

CREATE TABLE `cek_pembayaran` (
  `nisn` varchar(10) NOT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `tgl_sekarang` date DEFAULT NULL,
  `status_pembayaran` enum('Belum Lunas','Sudah Lunas','','') DEFAULT NULL,
  `jumlah_bulan` varchar(5) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `no_tlp` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cek_pembayaran`
--

INSERT INTO `cek_pembayaran` (`nisn`, `tgl_terakhir_bayar`, `tgl_sekarang`, `status_pembayaran`, `jumlah_bulan`, `nama`, `no_tlp`) VALUES
('123', '2026-05-25', '2026-05-25', 'Sudah Lunas', '1', 'sabil', '089732593'),
('999', '2026-05-25', '2026-05-25', 'Sudah Lunas', '1', 'Ghea', '0859437594');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kelas`
--

CREATE TABLE `tb_kelas` (
  `id_kelas` varchar(11) NOT NULL,
  `nama_kelas` varchar(10) DEFAULT NULL,
  `komp_keahlian` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_kelas`
--

INSERT INTO `tb_kelas` (`id_kelas`, `nama_kelas`, `komp_keahlian`) VALUES
('1', '10A', 'IPA'),
('123', '11A', 'MTK'),
('2', '10B', 'IPS');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pembayaran`
--

CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` varchar(11) NOT NULL,
  `status` enum('Belum Lunas','Sudah Lunas','','') DEFAULT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `tgl_bayar` date NOT NULL,
  `tgl_terakhir_bayar` date DEFAULT NULL,
  `batas_pembayaran` date DEFAULT NULL,
  `jumlah_bulan` varchar(10) DEFAULT NULL,
  `id_spp` varchar(40) DEFAULT NULL,
  `nominal_bayar` varchar(100) DEFAULT NULL,
  `jumlah_bayar` varchar(40) DEFAULT NULL,
  `kembalian` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pembayaran`
--

INSERT INTO `tb_pembayaran` (`id_pembayaran`, `status`, `nisn`, `tgl_bayar`, `tgl_terakhir_bayar`, `batas_pembayaran`, `jumlah_bulan`, `id_spp`, `nominal_bayar`, `jumlah_bayar`, `kembalian`) VALUES
('1', 'Sudah Lunas', '123', '2026-05-25', '2026-05-25', '2026-05-14', '1', '1', '100000', '100000', '0'),
('2', 'Sudah Lunas', '999', '2026-05-25', '2026-05-25', '2026-05-22', '1', '1', '100000', '60000', '0'),
('3', 'Sudah Lunas', '999', '2026-05-25', '2026-05-25', '2026-05-25', '1', '1', '100000', '40000', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tb_petugas`
--

CREATE TABLE `tb_petugas` (
  `id_petugas` varchar(11) NOT NULL,
  `username` varchar(25) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `nama_petugas` varchar(35) DEFAULT NULL,
  `level` enum('admin','petugas','siswa','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_petugas`
--

INSERT INTO `tb_petugas` (`id_petugas`, `username`, `password`, `nama_petugas`, `level`) VALUES
('1', 'admin', 'admin', 'SabilahH', 'admin'),
('2', 'petugas', 'petugas', 'Salsa', 'petugas'),
('3', '123', 'siswa', 'sabil', 'siswa');

-- --------------------------------------------------------

--
-- Table structure for table `tb_siswa`
--

CREATE TABLE `tb_siswa` (
  `nisn` varchar(10) NOT NULL,
  `nis` varchar(8) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `id_kelas` varchar(11) DEFAULT NULL,
  `nama_kelas` varchar(10) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_tlp` varchar(13) DEFAULT NULL,
  `id_spp` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_siswa`
--

INSERT INTO `tb_siswa` (`nisn`, `nis`, `nama`, `id_kelas`, `nama_kelas`, `alamat`, `no_tlp`, `id_spp`) VALUES
('123', '111', 'sabil', '2', '10A', 'Bogor', '089732593', '1'),
('3000', '1293', 'Pika', '1', NULL, 'bogor\r\n', '894769436', '1'),
('999', '9090', 'Ghea', '1', NULL, 'tangsel\r\n', '0859437594', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tb_spp`
--

CREATE TABLE `tb_spp` (
  `id_spp` varchar(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `nominal` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_spp`
--

INSERT INTO `tb_spp` (`id_spp`, `tahun`, `nominal`) VALUES
('1', 2026, '100000'),
('2', 2027, '500000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cek_pembayaran`
--
ALTER TABLE `cek_pembayaran`
  ADD PRIMARY KEY (`nisn`),
  ADD UNIQUE KEY `nama` (`nama`,`no_tlp`),
  ADD UNIQUE KEY `no_tlp` (`no_tlp`);

--
-- Indexes for table `tb_kelas`
--
ALTER TABLE `tb_kelas`
  ADD PRIMARY KEY (`id_kelas`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`);

--
-- Indexes for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_spp` (`id_spp`),
  ADD KEY `idx_pembayaran_nisn` (`nisn`);

--
-- Indexes for table `tb_petugas`
--
ALTER TABLE `tb_petugas`
  ADD PRIMARY KEY (`id_petugas`);

--
-- Indexes for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD PRIMARY KEY (`nisn`),
  ADD UNIQUE KEY `nama` (`nama`,`no_tlp`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`),
  ADD UNIQUE KEY `no_tlp` (`no_tlp`),
  ADD KEY `idx_siswa_id_kelas` (`id_kelas`),
  ADD KEY `idx_siswa_id_spp` (`id_spp`);

--
-- Indexes for table `tb_spp`
--
ALTER TABLE `tb_spp`
  ADD PRIMARY KEY (`id_spp`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cek_pembayaran`
--
ALTER TABLE `cek_pembayaran`
  ADD CONSTRAINT `cek_pembayaran_ibfk_1` FOREIGN KEY (`nama`) REFERENCES `tb_siswa` (`nama`),
  ADD CONSTRAINT `cek_pembayaran_ibfk_2` FOREIGN KEY (`nisn`) REFERENCES `tb_siswa` (`nisn`),
  ADD CONSTRAINT `fk_cek_pembayaran_no_tlp` FOREIGN KEY (`no_tlp`) REFERENCES `tb_siswa` (`no_tlp`);

--
-- Constraints for table `tb_pembayaran`
--
ALTER TABLE `tb_pembayaran`
  ADD CONSTRAINT `fk_pembayaran_siswa` FOREIGN KEY (`nisn`) REFERENCES `tb_siswa` (`nisn`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_siswa`
--
ALTER TABLE `tb_siswa`
  ADD CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `tb_kelas` (`id_kelas`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_siswa_spp` FOREIGN KEY (`id_spp`) REFERENCES `tb_spp` (`id_spp`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_siswa_ibfk_3` FOREIGN KEY (`nama_kelas`) REFERENCES `tb_kelas` (`nama_kelas`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
