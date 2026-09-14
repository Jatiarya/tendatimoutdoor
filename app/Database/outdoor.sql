-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2026 at 10:48 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `outdoor`
--

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` int UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `kode_voucher` varchar(50) DEFAULT NULL,
  `tipe` enum('persen','nominal') NOT NULL,
  `nilai` decimal(12,2) NOT NULL,
  `minimal_belanja` decimal(12,2) DEFAULT NULL,
  `maks_potongan` decimal(12,2) DEFAULT NULL,
  `id_perlengkapan` int UNSIGNED DEFAULT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diskon`
--

INSERT INTO `diskon` (`id_diskon`, `nama`, `kode_voucher`, `tipe`, `nilai`, `minimal_belanja`, `maks_potongan`, `id_perlengkapan`, `tgl_mulai`, `tgl_selesai`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'Diskon 17 Agustus', 'MERDEKA80', 'persen', 17.00, 80000.00, 19998.00, NULL, '2026-08-01', '2026-08-17', 'aktif', 'Ayo guys nikmati diskon agustusan kali ini, pesan perlengkapan yang kalian butuhin!!!', '2026-08-01 03:02:04', '2026-08-09 01:30:55'),
(6, 'Disc 20Rb min Pembayaran 200rb', 'cashback 20rb', 'nominal', 20000.00, 200000.00, NULL, NULL, '2026-08-01', '2026-08-31', 'aktif', '', '2026-08-09 08:17:36', '2026-08-09 12:35:08'),
(7, 'Diskon 17 Agustus', 'MERDEKA17', 'persen', 17.00, 70000.00, 20000.00, NULL, '2026-08-01', '2026-08-21', 'aktif', '', '2026-08-20 19:52:47', '2026-08-20 19:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int UNSIGNED NOT NULL,
  `nama_foto` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id`, `nama_foto`, `foto`, `alt`, `created_at`, `updated_at`) VALUES
(1, 'Sunrise Bukit Anak Dara', '1786259106_1bfe50214d95107f63e0.jpg', 'sunrise-bukit-anakdara', '2026-08-09 05:20:55', '2026-08-09 07:05:06'),
(2, 'Sawah Sembalun', '1786259163_2c5bda8bb52c6cb300b5.jpg', 'sawah-sembalun', '2026-08-09 07:06:03', '2026-08-09 07:06:03'),
(4, 'Gunung Rinjani', '1786259218_940f572dc47aade3b0e9.jpg', 'gunung-rinjani', '2026-08-09 07:06:58', '2026-08-09 07:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `kd_kategori` char(25) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kd_kategori`, `nama_kategori`) VALUES
(1, 'KT001', 'Tas'),
(2, 'KT002', 'Sepatu'),
(3, 'KT003', 'Tenda'),
(6, 'KT004', 'Alat Grill');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int UNSIGNED NOT NULL,
  `id_sewa` int UNSIGNED NOT NULL,
  `id_user` int UNSIGNED NOT NULL,
  `id_perlengkapan` int UNSIGNED DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `nama_perlengkapan` varchar(255) NOT NULL,
  `jumlah` int NOT NULL,
  `tgl_sewa` date NOT NULL,
  `tgl_kembali_rencana` date NOT NULL,
  `tgl_kembali_aktual` date NOT NULL,
  `durasi_hari` int NOT NULL,
  `total_biaya` decimal(12,2) NOT NULL,
  `order_id` varchar(100) DEFAULT NULL,
  `kode_voucher` varchar(50) DEFAULT NULL,
  `nilai_potongan` decimal(12,2) DEFAULT '0.00',
  `jumlah_bayar` decimal(12,2) DEFAULT NULL,
  `metode_bayar` varchar(50) DEFAULT NULL,
  `status_bayar` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `id_sewa`, `id_user`, `id_perlengkapan`, `username`, `nama_perlengkapan`, `jumlah`, `tgl_sewa`, `tgl_kembali_rencana`, `tgl_kembali_aktual`, `durasi_hari`, `total_biaya`, `order_id`, `kode_voucher`, `nilai_potongan`, `jumlah_bayar`, `metode_bayar`, `status_bayar`, `created_at`) VALUES
(65, 95, 2, 30, 'jazly', 'Eiger 4P', 1, '2026-08-12', '2026-08-13', '2026-08-12', 1, 78850.00, 'TENDATIM-95-1786542586', 'MERDEKA80', 16150.00, 78850.00, 'cod', 'lunas', '2026-08-12 13:50:12'),
(66, 100, 2, 2, 'jazly', 'Sepatu Eiger', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 29616.00, 'TENDATIM-TRX-2-1786580523', 'MERDEKA80', 5384.00, 29616.00, 'cod', 'lunas', '2026-08-13 00:34:12'),
(67, 101, 2, 30, 'jazly', 'Eiger 4P', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 80386.00, 'TENDATIM-TRX-2-1786580523', 'MERDEKA80', 14614.00, 80386.00, 'cod', 'lunas', '2026-08-13 00:34:12'),
(68, 99, 2, 2, 'jazly', 'Sepatu Eiger', 2, '2026-08-13', '2026-08-14', '2026-08-13', 1, 70000.00, 'TENDATIM-99-1786580454', NULL, 0.00, 70000.00, 'cod', 'lunas', '2026-08-13 00:34:29'),
(69, 98, 2, 30, 'jazly', 'Eiger 4P', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 78850.00, 'TENDATIM-98-1786580423', 'MERDEKA80', 16150.00, 78850.00, 'cod', 'lunas', '2026-08-13 00:34:49'),
(70, 96, 2, 30, 'jazly', 'Eiger 4P', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 84445.00, 'TENDATIM-TRX-2-1786579615', 'MERDEKA80', 10555.00, 84445.00, 'cod', 'lunas', '2026-08-13 00:34:57'),
(71, 97, 2, 22, 'jazly', 'Tas Hydropack', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 75556.00, 'TENDATIM-TRX-2-1786579615', 'MERDEKA80', 9444.00, 75556.00, 'cod', 'lunas', '2026-08-13 00:34:57'),
(72, 102, 2, 2, 'jazly', 'Sepatu Eiger', 4, '2026-08-13', '2026-08-14', '2026-08-13', 1, 127556.00, 'TENDATIM-TRX-2-1786581791', 'cashback 20rb', 12444.00, 127556.00, 'Midtrans', 'lunas', '2026-08-13 00:44:22'),
(73, 103, 2, 22, 'jazly', 'Tas Hydropack', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 77444.00, 'TENDATIM-TRX-2-1786581791', 'cashback 20rb', 7556.00, 77444.00, 'Midtrans', 'lunas', '2026-08-13 00:44:22'),
(74, 106, 3, 22, 'munadi', 'Tas Hydropack', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 70550.00, 'TENDATIM-106-1786584392', 'MERDEKA80', 14450.00, 70550.00, 'cod', 'lunas', '2026-08-13 01:27:51'),
(75, 105, 3, 2, 'munadi', 'Sepatu Eiger', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 35000.00, 'TENDATIM-105-1786584280', NULL, 0.00, 35000.00, 'cod', 'lunas', '2026-08-13 01:28:13'),
(76, 104, 2, 2, 'jazly', 'Sepatu Eiger', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 35000.00, 'TENDATIM-104-1786584131', NULL, 0.00, 35000.00, 'cod', 'lunas', '2026-08-13 01:28:24'),
(77, 107, 2, 2, 'jazly', 'Sepatu Eiger', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 35000.00, 'TENDATIM-107-1786613410', NULL, 0.00, 35000.00, 'cod', 'lunas', '2026-08-13 17:43:17'),
(78, 109, 2, 22, 'jazly', 'Tas Hydropack', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 70550.00, 'TENDATIM-109-1786614118', 'MERDEKA80', 14450.00, 70550.00, 'cod', 'lunas', '2026-08-13 17:43:29'),
(79, 108, 2, 30, 'jazly', 'Eiger 4P', 1, '2026-08-13', '2026-08-14', '2026-08-13', 1, 78850.00, 'TENDATIM-108-1786613652', 'MERDEKA80', 16150.00, 78850.00, 'cod', 'lunas', '2026-08-13 17:43:40'),
(80, 114, 2, 2, 'jazly', 'Sepatu Eiger', 1, '2026-08-20', '2026-08-21', '2026-08-20', 1, 35000.00, 'TENDATIM-114-1787226269', NULL, 0.00, 35000.00, 'Midtrans', 'lunas', '2026-08-20 19:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int UNSIGNED NOT NULL,
  `tipe` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'penyewaan_baru',
  `judul` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pesan` text,
  `id_referensi` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id_notifikasi`, `tipe`, `judul`, `pesan`, `id_referensi`, `url`, `is_read`, `created_at`) VALUES
(7, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi.', 'TRX-2-1786579615', 'http://localhost:8080/penyewaan/ubah-transaksi/TRX-2-1786579615', 1, '2026-08-13 00:06:56'),
(8, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi.', 'SINGLE-98', 'http://localhost:8080/penyewaan/ubah-transaksi/SINGLE-98', 1, '2026-08-13 00:20:23'),
(9, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi.', 'SINGLE-99', 'http://localhost:8080/penyewaan/ubah-transaksi/SINGLE-99', 1, '2026-08-13 00:20:54'),
(10, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi.', 'TRX-2-1786580523', 'http://localhost:8080/penyewaan/ubah-transaksi/TRX-2-1786580523', 1, '2026-08-13 00:22:03'),
(11, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'TRX-2-1786581791', 'http://localhost:8080/pembayaran?identifier=TRX-2-1786581791', 1, '2026-08-13 00:43:11'),
(12, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-104', 'http://localhost:8080/pembayaran?identifier=SINGLE-104', 1, '2026-08-13 01:22:11'),
(13, 'penyewaan_baru', 'Pesanan baru masuk', 'munadi mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-105', 'http://localhost:8080/pembayaran?identifier=SINGLE-105', 1, '2026-08-13 01:24:40'),
(14, 'penyewaan_baru', 'Pesanan baru masuk', 'munadi mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-106', 'http://localhost:8080/pembayaran?identifier=SINGLE-106', 1, '2026-08-13 01:26:32'),
(15, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-107', 'http://localhost:8080/pembayaran?identifier=SINGLE-107', 1, '2026-08-13 09:30:10'),
(16, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-108', 'http://localhost:8080/pembayaran?identifier=SINGLE-108', 1, '2026-08-13 09:34:12'),
(17, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-109', 'http://localhost:8080/pembayaran?identifier=SINGLE-109', 1, '2026-08-13 17:41:58'),
(18, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-110', 'http://localhost:8080/pembayaran?identifier=SINGLE-110', 1, '2026-08-20 16:45:05'),
(19, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-111', 'http://localhost:8080/pembayaran?identifier=SINGLE-111', 1, '2026-08-20 17:16:08'),
(20, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-112', 'http://localhost:8080/pembayaran?identifier=SINGLE-112', 1, '2026-08-20 17:20:12'),
(21, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-113', 'http://localhost:8080/pembayaran?identifier=SINGLE-113', 1, '2026-08-20 19:44:09'),
(22, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'SINGLE-114', 'http://localhost:8080/pembayaran?identifier=SINGLE-114', 1, '2026-08-20 19:44:29'),
(23, 'penyewaan_baru', 'Pesanan baru masuk', 'jazly mengajukan penyewaan baru dan menunggu konfirmasi pembayaran.', 'TRX-2-1787226874', 'http://localhost:8080/pembayaran?identifier=TRX-2-1787226874', 1, '2026-08-20 19:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int UNSIGNED NOT NULL,
  `id_sewa` int UNSIGNED DEFAULT NULL,
  `kode_transaksi` varchar(60) DEFAULT NULL,
  `order_id` varchar(100) NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `id_diskon` int UNSIGNED DEFAULT NULL,
  `kode_voucher` varchar(50) DEFAULT NULL,
  `nilai_potongan` decimal(12,2) DEFAULT '0.00',
  `metode_bayar` varchar(50) DEFAULT NULL,
  `status_bayar` enum('pending','lunas','gagal') NOT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `payload_callback` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_sewa`, `kode_transaksi`, `order_id`, `jumlah_bayar`, `id_diskon`, `kode_voucher`, `nilai_potongan`, `metode_bayar`, `status_bayar`, `snap_token`, `payload_callback`, `created_at`, `updated_at`) VALUES
(72, 95, NULL, 'TENDATIM-95-1786542586', 78850.00, NULL, 'MERDEKA80', 16150.00, 'cod', 'lunas', NULL, NULL, '2026-08-12 13:49:46', NULL),
(73, NULL, 'TRX-2-1786579615', 'TENDATIM-TRX-2-1786579615', 160002.00, NULL, 'MERDEKA80', 19998.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 00:06:56', NULL),
(74, 98, NULL, 'TENDATIM-98-1786580423', 78850.00, NULL, 'MERDEKA80', 16150.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 00:20:23', NULL),
(75, 99, NULL, 'TENDATIM-99-1786580454', 70000.00, NULL, NULL, 0.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 00:20:54', NULL),
(76, NULL, 'TRX-2-1786580523', 'TENDATIM-TRX-2-1786580523', 110002.00, NULL, 'MERDEKA80', 19998.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 00:22:03', NULL),
(77, NULL, 'TRX-2-1786581791', 'TENDATIM-TRX-2-1786581791', 205000.00, NULL, 'cashback 20rb', 20000.00, 'Midtrans', 'lunas', 'cab37f25-c07a-41da-85ea-a29494f715c0', NULL, '2026-08-13 00:43:12', NULL),
(78, 104, NULL, 'TENDATIM-104-1786584131', 35000.00, NULL, NULL, 0.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 01:22:11', NULL),
(79, 105, NULL, 'TENDATIM-105-1786584280', 35000.00, NULL, NULL, 0.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 01:24:40', NULL),
(80, 106, NULL, 'TENDATIM-106-1786584392', 70550.00, NULL, 'MERDEKA80', 14450.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 01:26:32', NULL),
(81, 107, NULL, 'TENDATIM-107-1786613410', 35000.00, NULL, NULL, 0.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 09:30:10', NULL),
(82, 108, NULL, 'TENDATIM-108-1786613652', 78850.00, NULL, 'MERDEKA80', 16150.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 09:34:12', NULL),
(83, 109, NULL, 'TENDATIM-109-1786614118', 70550.00, NULL, 'MERDEKA80', 14450.00, 'cod', 'lunas', NULL, NULL, '2026-08-13 17:41:58', NULL),
(84, 110, NULL, 'TENDATIM-110-1787215505', 35000.00, NULL, NULL, 0.00, 'cod', 'gagal', NULL, NULL, '2026-08-20 16:45:07', NULL),
(85, 111, NULL, 'TENDATIM-111-1787217368', 85000.00, NULL, NULL, 0.00, 'cod', 'gagal', NULL, NULL, '2026-08-20 17:16:08', NULL),
(86, 112, NULL, 'TENDATIM-112-1787217612', 35000.00, NULL, NULL, 0.00, 'cod', 'gagal', NULL, NULL, '2026-08-20 17:20:12', NULL),
(87, 113, NULL, 'TENDATIM-113-1787226249', 35000.00, NULL, NULL, 0.00, NULL, 'gagal', '768798f2-2847-4af5-b2d6-1d0826153ad5', NULL, '2026-08-20 19:44:10', NULL),
(88, 114, NULL, 'TENDATIM-114-1787226269', 35000.00, NULL, NULL, 0.00, 'Midtrans', 'lunas', '12429cad-0d32-4a36-82a6-43d81a63cc00', NULL, '2026-08-20 19:44:30', NULL),
(89, NULL, 'TRX-2-1787226874', 'TENDATIM-TRX-2-1787226874', 58100.00, NULL, 'MERDEKA17', 11900.00, 'cod', 'lunas', NULL, NULL, '2026-08-20 19:54:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penyewaan`
--

CREATE TABLE `penyewaan` (
  `id_sewa` int NOT NULL,
  `id_user` int NOT NULL,
  `kode_transaksi` varchar(60) DEFAULT NULL,
  `id_perlengkapan` int NOT NULL,
  `jumlah` int NOT NULL,
  `tgl_sewa` date NOT NULL,
  `tgl_kembali_rencana` date NOT NULL,
  `tgl_kembali_aktual` date DEFAULT NULL,
  `status` enum('disewa','dikembalikan','terlambat','ditolak','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `foto_ktp` varchar(255) NOT NULL,
  `nomor_wa` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `penyewaan`
--

INSERT INTO `penyewaan` (`id_sewa`, `id_user`, `kode_transaksi`, `id_perlengkapan`, `jumlah`, `tgl_sewa`, `tgl_kembali_rencana`, `tgl_kembali_aktual`, `status`, `foto_ktp`, `nomor_wa`, `created_at`, `updated_at`) VALUES
(95, 2, NULL, 30, 1, '2026-08-12', '2026-08-13', '2026-08-12', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-12 13:49:46', '2026-08-12 13:50:12'),
(96, 2, 'TRX-2-1786579615', 30, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:06:56', '2026-08-13 00:34:57'),
(97, 2, 'TRX-2-1786579615', 22, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:06:56', '2026-08-13 00:34:57'),
(98, 2, NULL, 30, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:20:23', '2026-08-13 00:34:49'),
(99, 2, NULL, 2, 2, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:20:54', '2026-08-13 00:34:29'),
(100, 2, 'TRX-2-1786580523', 2, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:22:03', '2026-08-13 00:34:12'),
(101, 2, 'TRX-2-1786580523', 30, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:22:03', '2026-08-13 00:34:12'),
(102, 2, 'TRX-2-1786581791', 2, 4, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:43:11', '2026-08-13 00:44:22'),
(103, 2, 'TRX-2-1786581791', 22, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 00:43:11', '2026-08-13 00:44:22'),
(104, 2, NULL, 2, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 01:22:11', '2026-08-13 01:28:24'),
(105, 3, NULL, 2, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786289869_e423eb55f17f53baa2ad.jpg', NULL, '2026-08-13 01:24:40', '2026-08-13 01:28:13'),
(106, 3, NULL, 22, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786289869_e423eb55f17f53baa2ad.jpg', NULL, '2026-08-13 01:26:31', '2026-08-13 01:27:51'),
(107, 2, NULL, 2, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 09:30:10', '2026-08-13 17:43:17'),
(108, 2, NULL, 30, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 09:34:12', '2026-08-13 17:43:40'),
(109, 2, NULL, 22, 1, '2026-08-13', '2026-08-14', '2026-08-13', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-13 17:41:58', '2026-08-13 17:43:29'),
(110, 2, NULL, 2, 1, '2026-08-20', '2026-08-21', NULL, 'dibatalkan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 16:45:05', '2026-08-20 17:20:35'),
(111, 2, NULL, 22, 1, '2026-08-20', '2026-08-21', NULL, 'dibatalkan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 17:16:08', '2026-08-20 17:20:42'),
(112, 2, NULL, 2, 1, '2026-08-20', '2026-08-21', NULL, 'dibatalkan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 17:20:12', '2026-08-20 17:20:49'),
(113, 2, NULL, 2, 1, '2026-08-20', '2026-08-21', NULL, 'dibatalkan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 19:44:09', '2026-08-20 19:49:09'),
(114, 2, NULL, 2, 1, '2026-08-20', '2026-08-21', '2026-08-20', 'dikembalikan', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 19:44:29', '2026-08-20 19:50:08'),
(115, 2, 'TRX-2-1787226874', 2, 2, '2026-08-20', '2026-08-21', NULL, 'terlambat', '1786117984_e410337cf13bfcf4e711.jpg', '081973377504', '2026-08-20 19:54:34', '2026-08-23 10:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `perlengkapan`
--

CREATE TABLE `perlengkapan` (
  `id_perlengkapan` int NOT NULL,
  `kd_perlengkapan` char(8) NOT NULL,
  `nama_perlengkapan` varchar(35) NOT NULL,
  `id_kategori` int NOT NULL,
  `stok` int NOT NULL,
  `harga_sewa` int NOT NULL,
  `foto_perlengkapan` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `perlengkapan`
--

INSERT INTO `perlengkapan` (`id_perlengkapan`, `kd_perlengkapan`, `nama_perlengkapan`, `id_kategori`, `stok`, `harga_sewa`, `foto_perlengkapan`) VALUES
(1, 'OTD001', 'Carier Eiger 40L', 1, 1, 75000, '1784891797_ee50974432f8d9f1f636.jpeg'),
(2, 'OTD002', 'Sepatu Eiger', 2, 4, 35000, 'sepatu.jpeg'),
(22, 'OTD003', 'Tas Hydropack', 1, 5, 85000, 'tashydropack.png'),
(30, 'OTD004', 'Eiger 4P', 3, 2, 95000, '1784813637_61ae4c5839918098dc75.png');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `nomor_wa` varchar(20) DEFAULT NULL,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `status_ktp` enum('belum_upload','menunggu_konfirmasi','terverifikasi','ditolak') DEFAULT 'belum_upload',
  `catatan_ktp` text,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `level` varchar(25) NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `email`, `nomor_wa`, `foto_ktp`, `google_id`, `status_ktp`, `catatan_ktp`, `username`, `password`, `reset_token`, `reset_token_expires`, `level`, `created_at`, `updated_at`) VALUES
(1, 'tendatimoutdoor@gmail.com', NULL, NULL, NULL, 'belum_upload', NULL, 'Jati', '$2y$10$9UC4m4p5PGoodnS.7CQnpu8sJkZG2p1MVhXiozQ4PyJ9yS.hm9pNe', NULL, NULL, 'admin', '2026-08-04 02:45:50', '2026-08-07 07:45:13'),
(2, 'jazlyyy04@gmail.com', '081973377504', '1786117984_e410337cf13bfcf4e711.jpg', NULL, 'terverifikasi', NULL, 'jazly', '$2y$10$ngd4K6dhWM8cx7FzkcZX8u0JgkrXdF/K.8jIc9x3CpBS8eztqA1eu', NULL, NULL, 'user', '0000-00-00 00:00:00', '2026-08-20 08:42:02'),
(3, 'munadi@gmail.com', NULL, '1786289869_e423eb55f17f53baa2ad.jpg', NULL, 'ditolak', 'upload foto ktp yang sesuai', 'munadi', '$2y$10$C2lRNjWjLEMHbfS.4WOSYeSe0aTybETB1pOE321hCCtm8LpQ4OmSe', NULL, NULL, 'user', '2026-07-30 16:38:00', '2026-08-20 11:57:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`),
  ADD UNIQUE KEY `id_perlengkapan` (`id_perlengkapan`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD UNIQUE KEY `id_sewa` (`id_sewa`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`);

--
-- Indexes for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD PRIMARY KEY (`id_sewa`);

--
-- Indexes for table `perlengkapan`
--
ALTER TABLE `perlengkapan`
  ADD PRIMARY KEY (`id_perlengkapan`),
  ADD UNIQUE KEY `kd_perlengkapan` (`kd_perlengkapan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id_diskon` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `penyewaan`
--
ALTER TABLE `penyewaan`
  MODIFY `id_sewa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `perlengkapan`
--
ALTER TABLE `perlengkapan`
  MODIFY `id_perlengkapan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
