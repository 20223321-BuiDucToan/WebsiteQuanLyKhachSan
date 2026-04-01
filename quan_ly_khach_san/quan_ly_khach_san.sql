-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 01, 2026 lúc 06:11 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quan_ly_khach_san`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_dat_phong`
--

CREATE TABLE `chi_tiet_dat_phong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dat_phong_id` bigint(20) UNSIGNED NOT NULL,
  `phong_id` bigint(20) UNSIGNED NOT NULL,
  `gia_phong` decimal(12,2) NOT NULL,
  `so_dem` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `so_nguoi_lon` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `so_tre_em` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ngay_nhan_phong_thuc_te` datetime DEFAULT NULL,
  `ngay_tra_phong_thuc_te` datetime DEFAULT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'da_dat',
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dat_phong`
--

CREATE TABLE `dat_phong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_dat_phong` varchar(20) NOT NULL,
  `khach_hang_id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ngay_dat` datetime NOT NULL,
  `ngay_nhan_phong_du_kien` date NOT NULL,
  `ngay_tra_phong_du_kien` date NOT NULL,
  `ngay_nhan_phong_thuc_te` datetime DEFAULT NULL,
  `ngay_tra_phong_thuc_te` datetime DEFAULT NULL,
  `so_nguoi_lon` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `so_tre_em` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'cho_xac_nhan',
  `nguon_dat` varchar(30) NOT NULL DEFAULT 'truc_tiep',
  `yeu_cau_dac_biet` text DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dich_vu`
--

CREATE TABLE `dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_dich_vu` varchar(20) NOT NULL,
  `ten_dich_vu` varchar(100) NOT NULL,
  `loai_dich_vu` varchar(50) DEFAULT NULL,
  `don_vi_tinh` varchar(30) NOT NULL DEFAULT 'lan',
  `don_gia` decimal(12,2) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'hoat_dong',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_hoa_don` varchar(20) NOT NULL,
  `dat_phong_id` bigint(20) UNSIGNED NOT NULL,
  `tong_tien_phong` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_tien_dich_vu` decimal(12,2) NOT NULL DEFAULT 0.00,
  `giam_gia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `thue` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_tien` decimal(12,2) NOT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'chua_thanh_toan',
  `thoi_diem_xuat` datetime DEFAULT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_khach_hang` varchar(20) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `gioi_tinh` varchar(20) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_giay_to` varchar(50) DEFAULT NULL,
  `loai_giay_to` varchar(30) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `quoc_tich` varchar(50) DEFAULT NULL,
  `hang_khach_hang` varchar(30) NOT NULL DEFAULT 'thuong',
  `trang_thai` varchar(30) NOT NULL DEFAULT 'hoat_dong',
  `anh_dai_dien` varchar(255) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_phong`
--

CREATE TABLE `loai_phong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_loai_phong` varchar(20) NOT NULL,
  `ten_loai_phong` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia_mot_dem` decimal(12,2) NOT NULL,
  `so_nguoi_toi_da` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `dien_tich` decimal(8,2) DEFAULT NULL,
  `so_giuong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `loai_giuong` varchar(50) DEFAULT NULL,
  `so_phong_tam` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `co_ban_cong` tinyint(1) NOT NULL DEFAULT 0,
  `co_bep_rieng` tinyint(1) NOT NULL DEFAULT 0,
  `co_huong_bien` tinyint(1) NOT NULL DEFAULT 0,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'hoat_dong',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_phong`
--

INSERT INTO `loai_phong` (`id`, `ma_loai_phong`, `ten_loai_phong`, `mo_ta`, `gia_mot_dem`, `so_nguoi_toi_da`, `dien_tich`, `so_giuong`, `loai_giuong`, `so_phong_tam`, `co_ban_cong`, `co_bep_rieng`, `co_huong_bien`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'LPSTD', 'Standard', 'Phong tieu chuan cho 2 khach.', 550000.00, 2, 24.00, 1, 'Queen', 1, 0, 0, 0, 'hoat_dong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(2, 'LPDLX', 'Deluxe', 'Phong cao cap co cua so rong.', 850000.00, 2, 32.00, 1, 'King', 1, 1, 0, 0, 'hoat_dong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(3, 'LPSUT', 'Suite', 'Phong suite co khu tiep khach rieng.', 1300000.00, 3, 45.00, 1, 'King', 1, 1, 0, 1, 'hoat_dong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(4, 'LPFAM', 'Family', 'Phong gia dinh cho 4 khach.', 1550000.00, 4, 52.00, 2, 'Queen + Queen', 1, 1, 1, 0, 'hoat_dong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(5, 'LPPRE', 'Premium', 'Phong cao cap tam nhin dep.', 2100000.00, 4, 65.00, 2, 'King + Sofa bed', 2, 1, 1, 1, 'hoat_dong', '2026-04-01 05:27:31', '2026-04-01 05:27:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_03_24_231547_create_nguoi_dung_table', 1),
(2, '2026_03_24_231548_create_khach_hang_table', 1),
(3, '2026_03_24_231548_create_loai_phong_table', 1),
(4, '2026_03_24_231548_create_phong_table', 1),
(5, '2026_03_24_231549_create_dat_phong_table', 1),
(6, '2026_03_24_231549_create_t_dat_phong_table', 1),
(7, '2026_03_24_231550_create_dich_vu_table', 1),
(8, '2026_03_24_231550_create_su_dung_dich_vu_table', 1),
(9, '2026_03_24_231551_create_hoa_don_table', 1),
(10, '2026_03_24_231551_create_thanh_toan_table', 1),
(11, '2026_03_25_062844_create_sessions_table', 1),
(12, '2026_03_25_065752_create_password_reset_tokens_table', 1),
(13, '2026_03_25_072406_create_cache_table', 1),
(14, '2026_03_25_113227_update_nguoi_dung_table', 1),
(15, '2026_03_29_000000_add_khach_hang_role_to_nguoi_dung_table', 1),
(16, '2026_04_01_120000_fix_password_reset_tokens_structure', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `anh_dai_dien` varchar(255) DEFAULT NULL,
  `vai_tro` enum('admin','nhan_vien','khach_hang') NOT NULL DEFAULT 'nhan_vien',
  `trang_thai` enum('hoat_dong','tam_khoa') NOT NULL DEFAULT 'hoat_dong',
  `lan_dang_nhap_cuoi` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ho_ten`, `ten_dang_nhap`, `email`, `password`, `so_dien_thoai`, `dia_chi`, `anh_dai_dien`, `vai_tro`, `trang_thai`, `lan_dang_nhap_cuoi`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Quan tri he thong', 'admin', 'admin@gmail.com', '$2y$12$vO4OaHup555mhPgvnk0FxOm6tILcdlBQrhVpPc5qpc37.kZ454wQq', '0900000000', NULL, NULL, 'admin', 'hoat_dong', NULL, NULL, '2026-04-01 05:27:31', '2026-04-01 05:27:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong`
--

CREATE TABLE `phong` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_phong` varchar(20) NOT NULL,
  `so_phong` varchar(20) NOT NULL,
  `loai_phong_id` bigint(20) UNSIGNED NOT NULL,
  `tang` int(10) UNSIGNED DEFAULT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'trong',
  `tinh_trang_ve_sinh` varchar(30) NOT NULL DEFAULT 'sach',
  `tinh_trang_hoat_dong` varchar(30) NOT NULL DEFAULT 'hoat_dong',
  `gia_mac_dinh` decimal(12,2) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phong`
--

INSERT INTO `phong` (`id`, `ma_phong`, `so_phong`, `loai_phong_id`, `tang`, `trang_thai`, `tinh_trang_ve_sinh`, `tinh_trang_hoat_dong`, `gia_mac_dinh`, `ghi_chu`, `created_at`, `updated_at`) VALUES
(1, 'PH101', '101', 1, 1, 'trong', 'sach', 'hoat_dong', 550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(2, 'PH102', '102', 1, 1, 'da_dat', 'sach', 'hoat_dong', 550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(3, 'PH103', '103', 1, 1, 'dang_su_dung', 'can_don', 'hoat_dong', 550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(4, 'PH104', '104', 2, 1, 'trong', 'sach', 'hoat_dong', 850000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(5, 'PH105', '105', 2, 1, 'bao_tri', 'dang_don', 'tam_ngung', 850000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(6, 'PH201', '201', 1, 2, 'trong', 'sach', 'hoat_dong', 550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(7, 'PH202', '202', 1, 2, 'da_dat', 'sach', 'hoat_dong', 550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(8, 'PH203', '203', 2, 2, 'dang_su_dung', 'can_don', 'hoat_dong', 850000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(9, 'PH204', '204', 2, 2, 'don_dep', 'dang_don', 'hoat_dong', 850000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(10, 'PH205', '205', 3, 2, 'trong', 'sach', 'hoat_dong', 1300000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(11, 'PH301', '301', 2, 3, 'trong', 'sach', 'hoat_dong', 850000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(12, 'PH302', '302', 3, 3, 'da_dat', 'sach', 'hoat_dong', 1300000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(13, 'PH303', '303', 3, 3, 'dang_su_dung', 'ban', 'hoat_dong', 1300000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(14, 'PH304', '304', 4, 3, 'trong', 'sach', 'hoat_dong', 1550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(15, 'PH401', '401', 3, 4, 'trong', 'sach', 'hoat_dong', 1300000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(16, 'PH402', '402', 4, 4, 'da_dat', 'sach', 'hoat_dong', 1550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(17, 'PH403', '403', 4, 4, 'don_dep', 'dang_don', 'hoat_dong', 1550000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(18, 'PH501', '501', 5, 5, 'trong', 'sach', 'hoat_dong', 2100000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(19, 'PH502', '502', 5, 5, 'dang_su_dung', 'can_don', 'hoat_dong', 2100000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31'),
(20, 'PH503', '503', 5, 5, 'bao_tri', 'can_don', 'tam_ngung', 2100000.00, 'Du lieu test cho man hinh quan ly phong', '2026-04-01 05:27:31', '2026-04-01 05:27:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ZIGiyXCXroB4cbB1qFtxpNhwV3oaIEnNXcZpx49P', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0U0aVZybGFKOWd4S2pYRGdMODJaRGFSM0pGSDVycW5CYWk4TzhEeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8/cGFnZT0yIjtzOjU6InJvdXRlIjtzOjEzOiJib29raW5nLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775047877);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `su_dung_dich_vu`
--

CREATE TABLE `su_dung_dich_vu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dat_phong_id` bigint(20) UNSIGNED NOT NULL,
  `dich_vu_id` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `don_gia` decimal(12,2) NOT NULL,
  `thanh_tien` decimal(12,2) NOT NULL,
  `thoi_diem_su_dung` datetime NOT NULL,
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_thanh_toan` varchar(20) NOT NULL,
  `hoa_don_id` bigint(20) UNSIGNED NOT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `phuong_thuc_thanh_toan` varchar(50) NOT NULL,
  `thoi_diem_thanh_toan` datetime NOT NULL,
  `trang_thai` varchar(30) NOT NULL DEFAULT 'thanh_cong',
  `nguoi_tao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Chỉ mục cho bảng `chi_tiet_dat_phong`
--
ALTER TABLE `chi_tiet_dat_phong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chi_tiet_dat_phong_dat_phong_id_foreign` (`dat_phong_id`),
  ADD KEY `chi_tiet_dat_phong_phong_id_foreign` (`phong_id`);

--
-- Chỉ mục cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dat_phong_ma_dat_phong_unique` (`ma_dat_phong`),
  ADD KEY `dat_phong_khach_hang_id_foreign` (`khach_hang_id`),
  ADD KEY `dat_phong_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `dich_vu`
--
ALTER TABLE `dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dich_vu_ma_dich_vu_unique` (`ma_dich_vu`);

--
-- Chỉ mục cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hoa_don_ma_hoa_don_unique` (`ma_hoa_don`),
  ADD KEY `hoa_don_dat_phong_id_foreign` (`dat_phong_id`),
  ADD KEY `hoa_don_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `khach_hang_ma_khach_hang_unique` (`ma_khach_hang`);

--
-- Chỉ mục cho bảng `loai_phong`
--
ALTER TABLE `loai_phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loai_phong_ma_loai_phong_unique` (`ma_loai_phong`),
  ADD UNIQUE KEY `loai_phong_ten_loai_phong_unique` (`ten_loai_phong`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nguoi_dung_ten_dang_nhap_unique` (`ten_dang_nhap`),
  ADD UNIQUE KEY `nguoi_dung_email_unique` (`email`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_tokens_email_index` (`email`);

--
-- Chỉ mục cho bảng `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phong_ma_phong_unique` (`ma_phong`),
  ADD UNIQUE KEY `phong_so_phong_unique` (`so_phong`),
  ADD KEY `phong_loai_phong_id_foreign` (`loai_phong_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `su_dung_dich_vu`
--
ALTER TABLE `su_dung_dich_vu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `su_dung_dich_vu_dat_phong_id_foreign` (`dat_phong_id`),
  ADD KEY `su_dung_dich_vu_dich_vu_id_foreign` (`dich_vu_id`),
  ADD KEY `su_dung_dich_vu_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `thanh_toan_ma_thanh_toan_unique` (`ma_thanh_toan`),
  ADD KEY `thanh_toan_hoa_don_id_foreign` (`hoa_don_id`),
  ADD KEY `thanh_toan_nguoi_tao_id_foreign` (`nguoi_tao_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_tiet_dat_phong`
--
ALTER TABLE `chi_tiet_dat_phong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dich_vu`
--
ALTER TABLE `dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loai_phong`
--
ALTER TABLE `loai_phong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phong`
--
ALTER TABLE `phong`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `su_dung_dich_vu`
--
ALTER TABLE `su_dung_dich_vu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_dat_phong`
--
ALTER TABLE `chi_tiet_dat_phong`
  ADD CONSTRAINT `chi_tiet_dat_phong_dat_phong_id_foreign` FOREIGN KEY (`dat_phong_id`) REFERENCES `dat_phong` (`id`),
  ADD CONSTRAINT `chi_tiet_dat_phong_phong_id_foreign` FOREIGN KEY (`phong_id`) REFERENCES `phong` (`id`);

--
-- Các ràng buộc cho bảng `dat_phong`
--
ALTER TABLE `dat_phong`
  ADD CONSTRAINT `dat_phong_khach_hang_id_foreign` FOREIGN KEY (`khach_hang_id`) REFERENCES `khach_hang` (`id`),
  ADD CONSTRAINT `dat_phong_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_dat_phong_id_foreign` FOREIGN KEY (`dat_phong_id`) REFERENCES `dat_phong` (`id`),
  ADD CONSTRAINT `hoa_don_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `phong`
--
ALTER TABLE `phong`
  ADD CONSTRAINT `phong_loai_phong_id_foreign` FOREIGN KEY (`loai_phong_id`) REFERENCES `loai_phong` (`id`);

--
-- Các ràng buộc cho bảng `su_dung_dich_vu`
--
ALTER TABLE `su_dung_dich_vu`
  ADD CONSTRAINT `su_dung_dich_vu_dat_phong_id_foreign` FOREIGN KEY (`dat_phong_id`) REFERENCES `dat_phong` (`id`),
  ADD CONSTRAINT `su_dung_dich_vu_dich_vu_id_foreign` FOREIGN KEY (`dich_vu_id`) REFERENCES `dich_vu` (`id`),
  ADD CONSTRAINT `su_dung_dich_vu_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_hoa_don_id_foreign` FOREIGN KEY (`hoa_don_id`) REFERENCES `hoa_don` (`id`),
  ADD CONSTRAINT `thanh_toan_nguoi_tao_id_foreign` FOREIGN KEY (`nguoi_tao_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
