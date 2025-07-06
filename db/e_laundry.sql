-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 08:32 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_laundry`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nm_barang` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nm_barang`, `stock`) VALUES
(1, 'Deterjen', 93),
(2, 'Pelembut', 93),
(3, 'Pewangi', 92);

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_barang_keluar` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tgl_keluar` date NOT NULL,
  `jml_keluar` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barang_keluar`
--

INSERT INTO `barang_keluar` (`id_barang_keluar`, `id_barang`, `tgl_keluar`, `jml_keluar`, `id_transaksi`) VALUES
(3, 3, '2025-06-25', 1, 0),
(4, 2, '2025-06-25', 1, 0),
(5, 1, '2025-06-25', 1, 0),
(6, 3, '2025-07-02', 1, 0),
(7, 2, '2025-07-02', 1, 0),
(8, 1, '2025-07-02', 1, 0),
(9, 3, '2025-07-02', 1, 0),
(10, 2, '2025-07-02', 1, 0),
(11, 1, '2025-07-02', 1, 0),
(12, 3, '2025-07-02', 1, 12),
(13, 2, '2025-07-02', 1, 12),
(14, 1, '2025-07-02', 1, 12),
(15, 3, '2025-07-07', 2, 13),
(16, 2, '2025-07-07', 2, 13),
(17, 1, '2025-07-07', 2, 13),
(18, 3, '2025-07-07', 3, 14),
(19, 1, '2025-07-07', 1, 15),
(20, 2, '2025-07-07', 1, 15),
(21, 1, '2025-07-07', 3, 16),
(22, 2, '2025-07-07', 3, 16),
(23, 3, '2025-07-07', 3, 16);

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_barang_masuk` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `jml_masuk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_layanan`
--

CREATE TABLE `jenis_layanan` (
  `id_jns_layanan` int(11) NOT NULL,
  `id_kategori_layanan` int(11) NOT NULL,
  `jenis_layanan` varchar(255) NOT NULL,
  `estimasi_waktu` int(11) NOT NULL,
  `tarif` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis_layanan`
--

INSERT INTO `jenis_layanan` (`id_jns_layanan`, `id_kategori_layanan`, `jenis_layanan`, `estimasi_waktu`, `tarif`) VALUES
(2, 5, 'Cuci + Setrika', 3, 50000),
(3, 5, 'Hanya Cuci', 3, 50000),
(4, 5, 'Setrika', 1, 50000);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_layanan`
--

CREATE TABLE `kategori_layanan` (
  `id_kategori_layanan` int(11) NOT NULL,
  `nm_layanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_layanan`
--

INSERT INTO `kategori_layanan` (`id_kategori_layanan`, `nm_layanan`) VALUES
(5, 'Laundry Kiloan'),
(6, 'Laundry Satuan');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nm_pelanggan` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `kecamatan` varchar(255) NOT NULL,
  `alamat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `id_user`, `nm_pelanggan`, `no_telp`, `kecamatan`, `alamat`) VALUES
(6, 0, 'asda', '08231', 'KEBON JERUK', 'sdas');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_jenis_layanan` int(11) NOT NULL,
  `status_bayar` varchar(255) NOT NULL,
  `status_ambil` varchar(255) NOT NULL,
  `tgl_transaksi` datetime NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `antar_jemput` varchar(255) NOT NULL,
  `alamat_jemput` text NOT NULL,
  `invoice` varchar(255) NOT NULL,
  `berat` varchar(255) NOT NULL,
  `tgl_diambil` datetime NOT NULL,
  `bukti` text NOT NULL,
  `total` varchar(255) NOT NULL,
  `metode_bayar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `id_user`, `id_jenis_layanan`, `status_bayar`, `status_ambil`, `tgl_transaksi`, `tgl_mulai`, `tgl_selesai`, `antar_jemput`, `alamat_jemput`, `invoice`, `berat`, `tgl_diambil`, `bukti`, `total`, `metode_bayar`) VALUES
(14, 6, 1, 4, 'Belum', 'Proses', '2025-07-07 01:09:07', '2025-07-07', '2025-07-08', 'Tidak', 'Jln. Soekarno Hatta No. 1, Jakarta Selatan', '20250707010907', '3', '0000-00-00 00:00:00', '', '150000', 'Tunai'),
(15, 6, 1, 3, 'Belum', 'Proses', '2025-07-07 01:11:20', '2025-07-07', '2025-07-10', 'Tidak', 'Jln. Soekarno Hatta No. 1, Jakarta Selatan', '20250707011120', '2', '0000-00-00 00:00:00', '', '100000', 'Tunai'),
(16, 6, 1, 2, 'Belum', 'Proses', '2025-07-07 01:32:00', '2025-07-07', '2025-07-10', 'Tidak', 'Jln. Soekarno Hatta No. 1, Jakarta Selatan', '20250707013200', '2', '0000-00-00 00:00:00', '', '100000', 'Tunai');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nm_user` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `tgl_daftar` date NOT NULL,
  `level_akses` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nm_user`, `username`, `password`, `tgl_daftar`, `level_akses`) VALUES
(1, 'Admin', 'admin', '$2y$10$yCUIMaN7pz9iDz/boG8GiumoFY4.TX8YBeygzDUe2qSOXONAo2vNa', '2025-06-05', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_barang_keluar`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_barang_masuk`);

--
-- Indexes for table `jenis_layanan`
--
ALTER TABLE `jenis_layanan`
  ADD PRIMARY KEY (`id_jns_layanan`);

--
-- Indexes for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  ADD PRIMARY KEY (`id_kategori_layanan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id_barang_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_barang_masuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jenis_layanan`
--
ALTER TABLE `jenis_layanan`
  MODIFY `id_jns_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  MODIFY `id_kategori_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
