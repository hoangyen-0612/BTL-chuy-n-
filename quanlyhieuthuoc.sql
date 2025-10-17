-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2025 at 02:30 AM
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
-- Database: `quanlyhieuthuoc`
--

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc_thuoc`
--

CREATE TABLE `danh_muc_thuoc` (
  `ma_danh_muc` varchar(10) NOT NULL,
  `ten_danh_muc` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `danh_muc_thuoc`
--

INSERT INTO `danh_muc_thuoc` (`ma_danh_muc`, `ten_danh_muc`, `mo_ta`, `created_at`, `updated_at`) VALUES
('DM001', 'Thuốc giảm đau', 'Các loại thuốc giảm đau, hạ sốt', '2025-08-28 09:40:47', '2025-09-24 16:09:46'),
('DM002', 'Kháng sinh', 'Thuốc kháng sinh điều trị nhiễm khuẩn', '2025-08-28 09:40:47', '2025-09-24 16:25:54'),
('DM003', 'Thuốc tim mạch', 'Thuốc điều trị bệnh tim mạch', '2025-08-28 09:40:47', '2025-09-24 16:26:30'),
('DM004', 'Thuốc tiêu hóa', 'Thuốc hỗ trợ tiêu hóa', '2025-08-28 09:40:47', '2025-09-24 16:26:55'),
('DM005', 'Vitamin & khoáng chất', 'Thực phẩm chức năng, vitamin', '2025-08-28 09:40:47', '2025-09-24 16:27:23'),
('DM006', 'Thuốc da liễu', 'Thuốc điều trị bệnh ngoài da', '2025-08-28 09:40:47', '2025-09-24 16:27:52'),
('DM007', 'Thuốc mắt tai mũi họng', 'Thuốc chuyên khoa', '2025-08-28 09:40:47', '2025-09-24 16:28:26'),
('DM008', 'Thuốc thần kinh', 'Thuốc điều trị bệnh thần kinh', '2025-08-28 09:40:47', '2025-09-24 16:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `dondat`
--

CREATE TABLE `dondat` (
  `so_don_dat` varchar(10) NOT NULL,
  `ngay_dat` date NOT NULL,
  `ma_khach` varchar(10) NOT NULL,
  `ma_thuoc` varchar(10) NOT NULL,
  `ten_thuoc` varchar(255) NOT NULL,
  `loai_thuoc` varchar(255) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_ban` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(15,2) GENERATED ALWAYS AS (`so_luong` * `gia_ban`) STORED,
  `trang_thai` enum('Chờ xử lý','Đã xác nhận','Đã giao','Đã hủy') DEFAULT 'Chờ xử lý',
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dondat`
--

INSERT INTO `dondat` (`so_don_dat`, `ngay_dat`, `ma_khach`, `ma_thuoc`, `ten_thuoc`, `loai_thuoc`, `so_luong`, `gia_ban`, `trang_thai`, `ghi_chu`, `created_at`, `updated_at`) VALUES
('DD004', '2024-12-09', 'KH002', 'T005', 'Vitamin C 500mg', 'Vitamin & khoáng chất', 20, 3000.00, 'Đã giao', 'Tăng cường sức khỏe', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD005', '2024-12-10', 'KH003', 'T004', 'Omeprazole 20mg', 'Thuốc tiêu hóa', 6, 15000.00, 'Đã xác nhận', 'Điều trị dạ dày', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD006', '2024-12-10', 'KH003', 'T006', 'Ibuprofen 400mg', 'Thuốc giảm đau', 12, 7000.00, 'Đã xác nhận', 'Giảm đau khớp', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD007', '2024-12-11', 'KH004', 'T007', 'Cefixime 200mg', 'Kháng sinh', 3, 25000.00, 'Chờ xử lý', 'Kháng sinh mạnh', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD008', '2024-12-11', 'KH004', 'T008', 'Atorvastatin 20mg', 'Thuốc tim mạch', 4, 18000.00, 'Chờ xử lý', 'Điều trị cholesterol', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD009', '2024-12-12', 'KH005', 'T009', 'Betamethasone Cream', 'Thuốc da liễu', 2, 22000.00, 'Đã xác nhận', 'Điều trị viêm da', '2025-08-28 09:42:58', '2025-08-28 09:42:58'),
('DD010', '2024-12-12', 'KH005', 'T010', 'Cetirizine 10mg', 'Thuốc mắt tai mũi họng', 18, 6000.00, 'Đã xác nhận', 'Dị ứng mùa', '2025-08-28 09:42:58', '2025-09-05 12:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `donmua`
--

CREATE TABLE `donmua` (
  `so_don_mua` varchar(10) NOT NULL,
  `ngay_mua` date NOT NULL,
  `ma_nha_cung_cap` varchar(10) NOT NULL,
  `ma_thuoc` varchar(10) NOT NULL,
  `ten_thuoc` varchar(255) NOT NULL,
  `loai_thuoc` varchar(255) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_mua` decimal(10,2) NOT NULL,
  `thanh_tien` decimal(15,2) GENERATED ALWAYS AS (`so_luong` * `gia_mua`) STORED,
  `trang_thai` enum('Chờ xử lý','Đã xác nhận','Đã giao','Đã hủy') DEFAULT 'Chờ xử lý',
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donmua`
--

INSERT INTO `donmua` (`so_don_mua`, `ngay_mua`, `ma_nha_cung_cap`, `ma_thuoc`, `ten_thuoc`, `loai_thuoc`, `so_luong`, `gia_mua`, `trang_thai`, `ghi_chu`, `created_at`, `updated_at`) VALUES
('DM004', '2024-12-04', 'NCC004', 'T004', 'Omeprazole 20mg', 'Thuốc tiêu hóa', 60, 12000.00, 'Đã xác nhận', 'Thuốc dạ dày', '2025-08-28 09:42:38', '2025-09-10 13:50:45'),
('DM005', '2024-12-05', 'NCC005', 'T005', 'Vitamin C 500mg', 'Vitamin & khoáng chất', 2000, 2000.00, 'Đã giao', 'Vitamin bán chạy', '2025-08-28 09:42:38', '2025-08-28 09:42:38'),
('DM006', '2024-12-06', 'NCC001', 'T006', 'Ibuprofen 400mg', 'Thuốc giảm đau', 400, 5000.00, 'Đã giao', 'Thuốc chống viêm', '2025-08-28 09:42:38', '2025-08-28 09:42:38'),
('DM007', '2024-12-07', 'NCC002', 'T007', 'Cefixime 200mg', 'Kháng sinh', 300, 18000.00, 'Chờ xử lý', 'Kháng sinh cao cấp', '2025-08-28 09:42:38', '2025-08-28 09:42:38'),
('DM008', '2024-12-08', 'NCC003', 'T008', 'Atorvastatin 20mg', 'Thuốc tim mạch', 250, 15000.00, 'Đã xác nhận', 'Thuốc mỡ máu', '2025-08-28 09:42:38', '2025-08-28 09:42:38'),
('DM009', '2024-12-09', 'NCC004', 'T009', 'Betamethasone Cream', 'Thuốc da liễu', 150, 18000.00, 'Đã giao', 'Thuốc bôi ngoài da', '2025-08-28 09:42:38', '2025-09-08 15:35:33');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `so_hd` varchar(10) NOT NULL,
  `ngay_ban` date NOT NULL,
  `ten_khach` varchar(255) NOT NULL,
  `ma_kho` varchar(10) DEFAULT NULL,
  `so_don_dat` varchar(10) DEFAULT NULL,
  `ma_thuoc` varchar(10) NOT NULL,
  `ten_thuoc` varchar(255) NOT NULL,
  `loai_thuoc` varchar(255) NOT NULL,
  `so_luong_ban` int(11) NOT NULL,
  `gia_ban` decimal(10,2) NOT NULL,
  `thanh_tien_ban` decimal(15,2) GENERATED ALWAYS AS (`so_luong_ban` * `gia_ban`) STORED,
  `ma_nhan_vien` varchar(10) NOT NULL,
  `phuong_thuc_thanh_toan` enum('Tiền mặt','Chuyển khoản','Thẻ') DEFAULT 'Tiền mặt',
  `giam_gia` decimal(10,2) DEFAULT 0.00,
  `thue_vat` decimal(5,2) DEFAULT 10.00,
  `tong_tien_cuoi` decimal(15,2) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trang_thai` enum('Chưa thanh toán','Đã thanh toán') DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`so_hd`, `ngay_ban`, `ten_khach`, `ma_kho`, `so_don_dat`, `ma_thuoc`, `ten_thuoc`, `loai_thuoc`, `so_luong_ban`, `gia_ban`, `ma_nhan_vien`, `phuong_thuc_thanh_toan`, `giam_gia`, `thue_vat`, `tong_tien_cuoi`, `ghi_chu`, `created_at`, `updated_at`, `trang_thai`) VALUES
('HD004', '2025-09-12', 'Trần Thị Bình', 'K005', 'DD004', 'T005', 'Vitamin C 500mg', 'Vitamin & khoáng chất', 20, 9000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, 66000.00, 'Mua số lượng lớn', '2025-08-28 09:43:37', '2025-09-22 16:12:10', 'Đã thanh toán'),
('HD005', '2025-09-12', 'Lê Văn Cường', 'K004', 'DD005', 'T004', 'Omeprazole 20mg', 'Thuốc tiêu hóa', 20, 15000.00, 'NV002', 'Tiền mặt', 0.00, 10.00, 99000.00, 'Thuốc dạ dày', '2025-08-28 09:43:37', '2025-09-24 13:47:24', 'Đã thanh toán'),
('HD006', '2025-09-09', 'Lê Văn Cường', 'K006', 'DD006', 'T006', 'Ibuprofen 400mg', 'Thuốc giảm đau', 20, 10000.00, 'NV002', 'Tiền mặt', 0.00, 10.00, 92400.00, 'Thuốc giảm đau khớp', '2025-08-28 09:43:37', '2025-09-24 13:47:30', 'Đã thanh toán'),
('HD007', '2025-09-09', 'Phạm Thị Dung', 'K007', 'DD007', 'T007', 'Cefixime 200mg', 'Kháng sinh', 20, 25000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, 82500.00, 'Kháng sinh cao cấp', '2025-08-28 09:43:37', '2025-09-24 13:47:43', 'Đã thanh toán'),
('HD008', '2025-09-10', 'Phạm Thị Dung', 'K008', 'DD008', 'T008', 'Atorvastatin 20mg', 'Thuốc tim mạch', 20, 18000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, 79200.00, 'Thuốc cholesterol', '2025-08-28 09:43:37', '2025-09-24 13:47:52', 'Đã thanh toán'),
('HD009', '2025-09-09', 'Hoàng Văn Em', 'K009', 'DD009', 'T009', 'Betamethasone Cream', 'Thuốc da liễu', 20, 22000.00, 'NV002', 'Tiền mặt', 0.00, 10.00, 48400.00, 'Thuốc bôi da', '2025-08-28 09:43:37', '2025-09-24 13:48:05', 'Đã thanh toán'),
('HD010', '2025-09-12', 'Hoàng Yến', 'K008', NULL, 'T008', '', '', 10, 18000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-12 05:13:12', '2025-09-26 14:13:27', 'Đã thanh toán'),
('HD011', '2025-09-12', 'Hoàng Yến', 'K009', NULL, 'T009', '', '', 30, 22000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-12 05:26:54', '2025-09-26 14:13:06', 'Đã thanh toán'),
('HD012', '2025-09-12', 'Hoàng Yến', 'K004', NULL, 'T004', '', '', 30, 15000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-12 05:27:24', '2025-09-26 14:13:17', 'Đã thanh toán'),
('HD013', '2025-09-17', 'Hoàng Yến', 'K009', NULL, 'T009', '', '', 5, 22000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-17 02:13:01', '2025-09-26 14:13:40', 'Đã thanh toán'),
('HD014', '2025-09-17', 'Hoàng Yến', 'K008', NULL, 'T008', '', '', 20, 18000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-17 02:43:06', '2025-09-26 14:13:55', 'Đã thanh toán'),
('HD015', '2025-09-26', 'Hoàng Yến', 'K006', NULL, 'T006', '', '', 1, 7000.00, 'NV001', 'Chuyển khoản', 0.00, 10.00, NULL, NULL, '2025-09-26 13:00:26', '2025-09-26 13:00:43', 'Đã thanh toán'),
('HD016', '2025-09-26', 'Phạm Thị Dung', 'K004', NULL, 'T004', '', '', 1, 15000.00, 'NV001', 'Tiền mặt', 0.00, 10.00, NULL, NULL, '2025-09-26 13:27:09', '2025-09-26 14:14:15', 'Đã thanh toán'),
('HD017', '2025-09-27', 'Lê Văn Cường', 'K011', NULL, 'T011', '', '', 1, 20000.00, 'NV001', 'Chuyển khoản', 0.00, 10.00, NULL, NULL, '2025-09-27 00:13:13', '2025-09-27 00:13:32', 'Đã thanh toán');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `ma_khach` varchar(10) NOT NULL,
  `ten_khach_hang` varchar(255) NOT NULL,
  `dia_chi` text NOT NULL,
  `dien_thoai` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`ma_khach`, `ten_khach_hang`, `dia_chi`, `dien_thoai`, `created_at`, `updated_at`) VALUES
('KH001', 'Nguyễn Văn An', '789 Đường Cầu Giấy, Cầu Giấy, Hà Nội', '0912345678', '2025-08-28 09:41:29', '2025-08-28 09:41:29'),
('KH002', 'Trần Thị Bình', '321 Lê Lợi, Q1, TP.HCM', '0987654321', '2025-08-28 09:41:29', '2025-08-28 09:41:29'),
('KH003', 'Lê Văn Cường', '555 Hoàng Hoa Thám, Ba Đình, Hà Nội', '0901234567', '2025-08-28 09:41:29', '2025-08-28 09:41:29'),
('KH004', 'Phạm Thị Dung', '777 Nguyễn Huệ, Q1, TP.HCM', '0909876543', '2025-08-28 09:41:29', '2025-08-28 09:41:29'),
('KH005', 'Hoàng Văn Em', '999 Trần Phú, Hai Bà Trưng, Hà Nội', '0911122334', '2025-08-28 09:41:29', '2025-08-28 09:41:29');

-- --------------------------------------------------------

--
-- Table structure for table `kho`
--

CREATE TABLE `kho` (
  `ma_kho` varchar(10) NOT NULL,
  `sl_nhap` int(11) NOT NULL DEFAULT 0,
  `sl_giao` int(11) NOT NULL DEFAULT 0,
  `ton_kho` int(11) GENERATED ALWAYS AS (`sl_nhap` - `sl_giao`) STORED,
  `gia_nhap` decimal(10,2) DEFAULT NULL,
  `vi_tri_kho` varchar(100) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kho`
--

INSERT INTO `kho` (`ma_kho`, `sl_nhap`, `sl_giao`, `gia_nhap`, `vi_tri_kho`, `ghi_chu`, `created_at`, `updated_at`) VALUES
('K004', 50, 21, 12000.00, 'Kệ C1', 'Thuốc tiêu hóa', '2025-08-28 09:42:17', '2025-09-26 13:27:09'),
('K005', 50, 40, 2000.00, 'Kệ D1', 'Vitamin bán chạy', '2025-08-28 09:42:17', '2025-09-26 12:56:24'),
('K006', 50, 41, 5000.00, 'Kệ A2', 'Thuốc giảm đau', '2025-08-28 09:42:17', '2025-09-26 13:00:26'),
('K007', 50, 40, 18000.00, 'Kệ B1', 'Kháng sinh cao cấp', '2025-08-28 09:42:17', '2025-09-26 12:54:02'),
('K008', 50, 35, 15000.00, 'Kệ C2', 'Thuốc mỡ máu', '2025-08-28 09:42:17', '2025-09-26 12:53:28'),
('K009', 50, 50, 18000.00, 'Kệ D2', 'Thuốc da liễu', '2025-08-28 09:42:17', '2025-09-18 03:24:14'),
('K010', 50, 20, 4000.00, 'Kệ E1', 'Thuốc dị ứng', '2025-08-28 09:42:17', '2025-09-26 12:54:41'),
('K011', 15, 1, NULL, NULL, NULL, '2025-09-18 03:22:30', '2025-09-27 00:13:13');

-- --------------------------------------------------------

--
-- Table structure for table `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `ma_nha_cung_cap` varchar(10) NOT NULL,
  `ten_nha_cung_cap` varchar(255) NOT NULL,
  `dia_chi` text NOT NULL,
  `dien_thoai` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nhacungcap`
--

INSERT INTO `nhacungcap` (`ma_nha_cung_cap`, `ten_nha_cung_cap`, `dia_chi`, `dien_thoai`, `created_at`, `updated_at`) VALUES
('NCC001', 'Công ty Dược phẩm Hà Tây', '123 Đường Láng, Đống Đa, Hà Nội', '0243856789', '2025-08-28 09:41:15', '2025-08-28 09:41:15'),
('NCC002', 'Công ty Dược Sài Gòn', '456 Nguyễn Thị Minh Khai, Q1, TP.HCM', '0287654321', '2025-08-28 09:41:15', '2025-08-28 09:41:15'),
('NCC003', 'Công ty Dược Traphaco', '75 Yên Ninh, Ba Đình, Hà Nội', '0243123456', '2025-08-28 09:41:15', '2025-08-28 09:41:15'),
('NCC004', 'Công ty Dược Imexpharm', '590 Cách Mạng Tháng 8, Q3, TP.HCM', '0289876543', '2025-08-28 09:41:15', '2025-08-28 09:41:15'),
('NCC005', 'Công ty Dược Domesco', '288 Bis Nguyễn Văn Cừ, Q1, TP.HCM', '0281234567', '2025-08-28 09:41:15', '2025-08-28 09:41:15');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE `nhanvien` (
  `ma_nhan_vien` varchar(10) NOT NULL,
  `ten_nhan_vien` varchar(255) NOT NULL,
  `dia_chi` text NOT NULL,
  `dien_thoai` varchar(15) NOT NULL,
  `chuc_vu` enum('Dược sĩ','Nhân viên bán hàng','Kế toán','Quản lý','Thủ kho') NOT NULL,
  `luong_co_ban` decimal(10,2) DEFAULT NULL,
  `ngay_vao_lam` date DEFAULT NULL,
  `trang_thai` enum('Đang làm việc','Nghỉ việc','Tạm nghỉ') DEFAULT 'Đang làm việc',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`ma_nhan_vien`, `ten_nhan_vien`, `dia_chi`, `dien_thoai`, `chuc_vu`, `luong_co_ban`, `ngay_vao_lam`, `trang_thai`, `created_at`, `updated_at`) VALUES
('NV001', 'Dược sĩ Nguyễn Thị Lan', '111 Đường Giải Phóng, Hai Bà Trưng, Hà Nội', '0901111111', 'Dược sĩ', 15000000.00, '2023-01-15', 'Đang làm việc', '2025-08-28 09:41:46', '2025-08-28 09:41:46'),
('NV002', 'Phạm Văn Minh', '222 Lý Thường Kiệt, Q10, TP.HCM', '0902222222', 'Nhân viên bán hàng', 8000000.00, '2023-03-20', 'Đang làm việc', '2025-08-28 09:41:46', '2025-08-28 09:41:46'),
('NV003', 'Trần Thị Nga', '333 Nguyễn Trãi, Thanh Xuân, Hà Nội', '0903333333', 'Kế toán', 12000000.00, '2023-02-10', 'Đang làm việc', '2025-08-28 09:41:46', '2025-08-28 09:41:46'),
('NV004', 'Lê Văn Ơn', '444 Điện Biên Phủ, Q3, TP.HCM', '0904444444', 'Quản lý', 20000000.00, '2022-12-01', 'Đang làm việc', '2025-08-28 09:41:46', '2025-08-28 09:41:46'),
('NV005', 'Vũ Thị Phương', '555 Tây Sơn, Đống Đa, Hà Nội', '0905555555', 'Nhân viên bán hàng', 9000000.00, '2023-04-05', 'Đang làm việc', '2025-08-28 09:41:46', '2025-09-17 09:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `phieunhap`
--

CREATE TABLE `phieunhap` (
  `so_pn` varchar(10) NOT NULL,
  `ngay_nhap` date NOT NULL,
  `ma_kho` varchar(10) NOT NULL,
  `so_don_mua` int(11) DEFAULT NULL,
  `ma_thuoc` varchar(10) NOT NULL,
  `ten_thuoc` varchar(255) NOT NULL,
  `loai_thuoc` varchar(255) NOT NULL,
  `so_luong_nhap` int(11) NOT NULL,
  `gia_nhap` decimal(10,2) NOT NULL,
  `thanh_tien_nhap` decimal(15,2) GENERATED ALWAYS AS (`so_luong_nhap` * `gia_nhap`) STORED,
  `trang_thai_nhan` enum('Chờ nhận','Đã nhận','Từ chối') DEFAULT 'Chờ nhận',
  `ma_nhan_vien` varchar(10) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ma_nha_cung_cap` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phieunhap`
--

INSERT INTO `phieunhap` (`so_pn`, `ngay_nhap`, `ma_kho`, `so_don_mua`, `ma_thuoc`, `ten_thuoc`, `loai_thuoc`, `so_luong_nhap`, `gia_nhap`, `trang_thai_nhan`, `ma_nhan_vien`, `ghi_chu`, `created_at`, `updated_at`, `ma_nha_cung_cap`) VALUES
('PN004', '2024-12-05', 'K004', 0, 'T004', 'Omeprazole 20mg', 'Thuốc tiêu hóa', 50, 10000.00, 'Chờ nhận', 'NV005', 'Chờ xác nhận chất lượng', '2025-08-28 09:43:16', '2025-09-12 12:24:45', 'NCC001'),
('PN005', '2024-12-06', 'K005', 0, 'T005', 'Vitamin C 500mg', 'Vitamin & khoáng chất', 50, 2000.00, 'Đã nhận', 'NV005', 'Hàng vitamin nhập nhiều', '2025-08-28 09:43:16', '2025-09-12 11:15:19', 'NCC002'),
('PN006', '2024-12-07', 'K006', 0, 'T006', 'Ibuprofen 400mg', 'Thuốc giảm đau', 50, 3000.00, 'Đã nhận', 'NV005', 'Thuốc chống viêm', '2025-08-28 09:43:16', '2025-09-12 12:24:58', 'NCC004'),
('PN007', '2024-12-08', 'K007', 0, 'T007', 'Cefixime 200mg', 'Kháng sinh', 50, 18000.00, 'Chờ nhận', 'NV005', 'Kháng sinh đắt tiền', '2025-08-28 09:43:16', '2025-09-12 11:12:28', 'NCC003'),
('PN008', '2024-12-09', 'K008', 0, 'T008', 'Atorvastatin 20mg', 'Thuốc tim mạch', 50, 12000.00, 'Đã nhận', 'NV005', 'Thuốc tim mạch cao cấp', '2025-08-28 09:43:16', '2025-09-12 12:25:24', 'NCC005'),
('PN009', '2024-12-10', 'K009', 0, 'T009', 'Betamethasone Cream', 'Thuốc da liễu', 50, 15000.00, 'Đã nhận', 'NV005', 'Thuốc bôi ngoài', '2025-08-28 09:43:16', '2025-09-12 12:25:44', 'NCC001'),
('PN010', '2025-09-12', '', NULL, 'T010', '', '', 50, 2000.00, 'Chờ nhận', 'NV004', NULL, '2025-09-12 12:09:20', '2025-09-12 12:25:52', 'NCC001'),
('PN011', '2025-09-18', '', NULL, 'T011', '', '', 15, 15000.00, 'Chờ nhận', 'NV003', NULL, '2025-09-18 03:23:17', '2025-09-18 03:23:17', 'NCC003');

-- --------------------------------------------------------

--
-- Table structure for table `thuoc`
--

CREATE TABLE `thuoc` (
  `ma_thuoc` varchar(10) NOT NULL,
  `ten_thuoc` varchar(255) NOT NULL,
  `ma_danh_muc` varchar(10) NOT NULL,
  `nha_san_xuat` varchar(255) NOT NULL,
  `gia_ban` decimal(10,2) NOT NULL,
  `han_su_dung` date NOT NULL,
  `hoat_chat` varchar(255) NOT NULL,
  `don_vi_tinh` varchar(50) DEFAULT 'Viên',
  `mo_ta` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ma_kho` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thuoc`
--

INSERT INTO `thuoc` (`ma_thuoc`, `ten_thuoc`, `ma_danh_muc`, `nha_san_xuat`, `gia_ban`, `han_su_dung`, `hoat_chat`, `don_vi_tinh`, `mo_ta`, `created_at`, `updated_at`, `ma_kho`) VALUES
('T004', 'Omeprazole 20mg', 'DM004', 'Domesco', 15000.00, '2025-11-20', 'Omeprazole', 'Vỉ', 'Thuốc điều trị loét dạ dày', '2025-08-28 09:42:02', '2025-09-12 11:19:58', 'K004'),
('T005', 'Vitamin C 500mg', 'DM005', 'Dược Sài Gòn', 3000.00, '2026-03-10', 'Acid Ascorbic', 'Viên', 'Bổ sung vitamin C', '2025-08-28 09:42:02', '2025-09-12 11:20:07', 'K005'),
('T006', 'Ibuprofen 400mg', 'DM001', 'Traphaco', 7000.00, '2025-08-25', 'Ibuprofen', 'Vi', 'Thuốc giảm đau chống viêm', '2025-08-28 09:42:02', '2025-09-12 11:20:16', 'K006'),
('T007', 'Cefixime 200mg', 'DM002', 'Imexpharm', 25000.00, '2025-07-18', 'Cefixime', 'Vỉ', 'Kháng sinh thế hệ 3', '2025-08-28 09:42:02', '2025-09-12 11:18:33', 'K007'),
('T008', 'Atorvastatin 20mg', 'DM003', 'Domesco', 18000.00, '2025-10-12', 'Atorvastatin', 'Viên', 'Thuốc điều trị mỡ máu cao', '2025-08-28 09:42:02', '2025-09-08 14:02:31', 'K008'),
('T009', 'Betamethasone Cream', 'DM006', 'Hà Tây', 22000.00, '2026-03-08', 'Betamethasone', 'Tuýp', 'Thuốc bôi da chống viêm', '2025-08-28 09:42:02', '2025-09-08 14:02:40', 'K009'),
('T010', 'Cetirizine 10mg', 'DM007', 'Sài Gòn', 6000.00, '2025-12-15', 'Cetirizine', 'Viên', 'Thuốc chống dị ứng', '2025-08-28 09:42:02', '2025-09-08 14:02:49', 'K010'),
('T011', 'Paracetamol 500mg', 'DM001', 'Hà Tây', 20000.00, '2026-10-10', 'Paracetamol', 'Vỉ', NULL, '2025-09-18 03:22:30', '2025-09-18 03:22:30', 'K011');

--
-- Triggers `thuoc`
--
DELIMITER $$
CREATE TRIGGER `auto_set_kho` BEFORE INSERT ON `thuoc` FOR EACH ROW BEGIN
    -- Nếu chưa nhập ma_kho thì tự sinh từ ma_thuoc
    IF NEW.ma_kho IS NULL OR NEW.ma_kho = '' THEN
        SET NEW.ma_kho = CONCAT('K', SUBSTRING(NEW.ma_thuoc, 2));
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_auto_set_kho` BEFORE INSERT ON `thuoc` FOR EACH ROW BEGIN
    DECLARE new_kho VARCHAR(10);

    -- Sinh mã kho từ mã thuốc
    SET new_kho = CONCAT('K', SUBSTRING(NEW.ma_thuoc, 2));

    -- Nếu chưa nhập ma_kho thì gán tự động
    IF NEW.ma_kho IS NULL OR NEW.ma_kho = '' THEN
        SET NEW.ma_kho = new_kho;
    END IF;

    -- Nếu mã kho chưa tồn tại trong bảng kho thì thêm mới
    IF NOT EXISTS (SELECT 1 FROM kho WHERE ma_kho = new_kho) THEN
        INSERT INTO kho(ma_kho) VALUES (new_kho);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(3, 'abc@gmail.com', '25d55ad283aa400af464c76d713c07ad'),
(4, 'hyen@gmail.com', 'eb439e876a2419da0d7674630e076a98'),
(5, 'admin@gmail.com', '781e5e245d69b566979b86e28d23f2c7'),
(6, 'ad@gmail.com', '25d55ad283aa400af464c76d713c07ad');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `danh_muc_thuoc`
--
ALTER TABLE `danh_muc_thuoc`
  ADD PRIMARY KEY (`ma_danh_muc`);

--
-- Indexes for table `dondat`
--
ALTER TABLE `dondat`
  ADD PRIMARY KEY (`so_don_dat`),
  ADD KEY `ma_khach` (`ma_khach`),
  ADD KEY `ma_thuoc` (`ma_thuoc`),
  ADD KEY `idx_ngay_dat` (`ngay_dat`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Indexes for table `donmua`
--
ALTER TABLE `donmua`
  ADD PRIMARY KEY (`so_don_mua`),
  ADD KEY `ma_nha_cung_cap` (`ma_nha_cung_cap`),
  ADD KEY `ma_thuoc` (`ma_thuoc`),
  ADD KEY `idx_ngay_mua` (`ngay_mua`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`so_hd`),
  ADD KEY `so_don_dat` (`so_don_dat`),
  ADD KEY `ma_thuoc` (`ma_thuoc`),
  ADD KEY `idx_ngay_ban` (`ngay_ban`),
  ADD KEY `idx_ma_nhan_vien` (`ma_nhan_vien`),
  ADD KEY `hoadon_ibfk_1` (`ma_kho`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`ma_khach`);

--
-- Indexes for table `kho`
--
ALTER TABLE `kho`
  ADD PRIMARY KEY (`ma_kho`),
  ADD KEY `idx_ton_kho` (`ton_kho`);

--
-- Indexes for table `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`ma_nha_cung_cap`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`ma_nhan_vien`),
  ADD KEY `idx_chuc_vu` (`chuc_vu`),
  ADD KEY `idx_trang_thai` (`trang_thai`);

--
-- Indexes for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`so_pn`),
  ADD KEY `ma_kho` (`ma_kho`),
  ADD KEY `so_don_mua` (`so_don_mua`),
  ADD KEY `ma_thuoc` (`ma_thuoc`),
  ADD KEY `ma_nhan_vien` (`ma_nhan_vien`),
  ADD KEY `idx_ngay_nhap` (`ngay_nhap`),
  ADD KEY `idx_trang_thai_nhan` (`trang_thai_nhan`);

--
-- Indexes for table `thuoc`
--
ALTER TABLE `thuoc`
  ADD PRIMARY KEY (`ma_thuoc`),
  ADD KEY `idx_ten_thuoc` (`ten_thuoc`),
  ADD KEY `idx_ma_danh_muc` (`ma_danh_muc`),
  ADD KEY `idx_han_su_dung` (`han_su_dung`),
  ADD KEY `fk_thuoc_kho` (`ma_kho`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dondat`
--
ALTER TABLE `dondat`
  ADD CONSTRAINT `dondat_ibfk_1` FOREIGN KEY (`ma_khach`) REFERENCES `khachhang` (`ma_khach`) ON DELETE CASCADE,
  ADD CONSTRAINT `dondat_ibfk_2` FOREIGN KEY (`ma_thuoc`) REFERENCES `thuoc` (`ma_thuoc`) ON DELETE CASCADE;

--
-- Constraints for table `donmua`
--
ALTER TABLE `donmua`
  ADD CONSTRAINT `donmua_ibfk_1` FOREIGN KEY (`ma_nha_cung_cap`) REFERENCES `nhacungcap` (`ma_nha_cung_cap`) ON DELETE CASCADE,
  ADD CONSTRAINT `donmua_ibfk_2` FOREIGN KEY (`ma_thuoc`) REFERENCES `thuoc` (`ma_thuoc`) ON DELETE CASCADE;

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`ma_kho`) REFERENCES `kho` (`ma_kho`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`so_don_dat`) REFERENCES `dondat` (`so_don_dat`) ON DELETE SET NULL,
  ADD CONSTRAINT `hoadon_ibfk_3` FOREIGN KEY (`ma_thuoc`) REFERENCES `thuoc` (`ma_thuoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `hoadon_ibfk_4` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhanvien` (`ma_nhan_vien`) ON DELETE CASCADE;

--
-- Constraints for table `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_3` FOREIGN KEY (`ma_thuoc`) REFERENCES `thuoc` (`ma_thuoc`) ON DELETE CASCADE,
  ADD CONSTRAINT `phieunhap_ibfk_4` FOREIGN KEY (`ma_nhan_vien`) REFERENCES `nhanvien` (`ma_nhan_vien`) ON DELETE SET NULL;

--
-- Constraints for table `thuoc`
--
ALTER TABLE `thuoc`
  ADD CONSTRAINT `fk_thuoc_kho` FOREIGN KEY (`ma_kho`) REFERENCES `kho` (`ma_kho`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `thuoc_ibfk_1` FOREIGN KEY (`ma_danh_muc`) REFERENCES `danh_muc_thuoc` (`ma_danh_muc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

