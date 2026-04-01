<?php

namespace Database\Seeders;

use App\Models\LoaiPhong;
use App\Models\Phong;
use Illuminate\Database\Seeder;

class PhongTestSeeder extends Seeder
{
    public function run(): void
    {
        $danhSachLoaiPhong = [
            [
                'ma_loai_phong' => 'LPSTD',
                'ten_loai_phong' => 'Standard',
                'mo_ta' => 'Phong tieu chuan cho 2 khach.',
                'gia_mot_dem' => 550000,
                'so_nguoi_toi_da' => 2,
                'dien_tich' => 24,
                'so_giuong' => 1,
                'loai_giuong' => 'Queen',
                'so_phong_tam' => 1,
                'co_ban_cong' => false,
                'co_bep_rieng' => false,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPDLX',
                'ten_loai_phong' => 'Deluxe',
                'mo_ta' => 'Phong cao cap co cua so rong.',
                'gia_mot_dem' => 850000,
                'so_nguoi_toi_da' => 2,
                'dien_tich' => 32,
                'so_giuong' => 1,
                'loai_giuong' => 'King',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => false,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPSUT',
                'ten_loai_phong' => 'Suite',
                'mo_ta' => 'Phong suite co khu tiep khach rieng.',
                'gia_mot_dem' => 1300000,
                'so_nguoi_toi_da' => 3,
                'dien_tich' => 45,
                'so_giuong' => 1,
                'loai_giuong' => 'King',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => false,
                'co_huong_bien' => true,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPFAM',
                'ten_loai_phong' => 'Family',
                'mo_ta' => 'Phong gia dinh cho 4 khach.',
                'gia_mot_dem' => 1550000,
                'so_nguoi_toi_da' => 4,
                'dien_tich' => 52,
                'so_giuong' => 2,
                'loai_giuong' => 'Queen + Queen',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => true,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPPRE',
                'ten_loai_phong' => 'Premium',
                'mo_ta' => 'Phong cao cap tam nhin dep.',
                'gia_mot_dem' => 2100000,
                'so_nguoi_toi_da' => 4,
                'dien_tich' => 65,
                'so_giuong' => 2,
                'loai_giuong' => 'King + Sofa bed',
                'so_phong_tam' => 2,
                'co_ban_cong' => true,
                'co_bep_rieng' => true,
                'co_huong_bien' => true,
                'trang_thai' => 'hoat_dong',
            ],
        ];

        $loaiPhongDaTao = [];

        foreach ($danhSachLoaiPhong as $loaiPhong) {
            $banGhi = LoaiPhong::query()->updateOrCreate(
                ['ma_loai_phong' => $loaiPhong['ma_loai_phong']],
                $loaiPhong
            );

            $loaiPhongDaTao[$loaiPhong['ma_loai_phong']] = $banGhi;
        }

        $danhSachPhong = [
            ['so_phong' => '101', 'ma_loai' => 'LPSTD', 'tang' => 1, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '102', 'ma_loai' => 'LPSTD', 'tang' => 1, 'trang_thai' => 'da_dat', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '103', 'ma_loai' => 'LPSTD', 'tang' => 1, 'trang_thai' => 'dang_su_dung', 'tinh_trang_ve_sinh' => 'can_don', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '104', 'ma_loai' => 'LPDLX', 'tang' => 1, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '105', 'ma_loai' => 'LPDLX', 'tang' => 1, 'trang_thai' => 'bao_tri', 'tinh_trang_ve_sinh' => 'dang_don', 'tinh_trang_hoat_dong' => 'tam_ngung'],
            ['so_phong' => '201', 'ma_loai' => 'LPSTD', 'tang' => 2, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '202', 'ma_loai' => 'LPSTD', 'tang' => 2, 'trang_thai' => 'da_dat', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '203', 'ma_loai' => 'LPDLX', 'tang' => 2, 'trang_thai' => 'dang_su_dung', 'tinh_trang_ve_sinh' => 'can_don', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '204', 'ma_loai' => 'LPDLX', 'tang' => 2, 'trang_thai' => 'don_dep', 'tinh_trang_ve_sinh' => 'dang_don', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '205', 'ma_loai' => 'LPSUT', 'tang' => 2, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '301', 'ma_loai' => 'LPDLX', 'tang' => 3, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '302', 'ma_loai' => 'LPSUT', 'tang' => 3, 'trang_thai' => 'da_dat', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '303', 'ma_loai' => 'LPSUT', 'tang' => 3, 'trang_thai' => 'dang_su_dung', 'tinh_trang_ve_sinh' => 'ban', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '304', 'ma_loai' => 'LPFAM', 'tang' => 3, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '401', 'ma_loai' => 'LPSUT', 'tang' => 4, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '402', 'ma_loai' => 'LPFAM', 'tang' => 4, 'trang_thai' => 'da_dat', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '403', 'ma_loai' => 'LPFAM', 'tang' => 4, 'trang_thai' => 'don_dep', 'tinh_trang_ve_sinh' => 'dang_don', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '501', 'ma_loai' => 'LPPRE', 'tang' => 5, 'trang_thai' => 'trong', 'tinh_trang_ve_sinh' => 'sach', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '502', 'ma_loai' => 'LPPRE', 'tang' => 5, 'trang_thai' => 'dang_su_dung', 'tinh_trang_ve_sinh' => 'can_don', 'tinh_trang_hoat_dong' => 'hoat_dong'],
            ['so_phong' => '503', 'ma_loai' => 'LPPRE', 'tang' => 5, 'trang_thai' => 'bao_tri', 'tinh_trang_ve_sinh' => 'can_don', 'tinh_trang_hoat_dong' => 'tam_ngung'],
        ];

        foreach ($danhSachPhong as $phong) {
            $loaiPhong = $loaiPhongDaTao[$phong['ma_loai']];

            Phong::query()->updateOrCreate(
                ['so_phong' => $phong['so_phong']],
                [
                    'ma_phong' => 'PH' . $phong['so_phong'],
                    'loai_phong_id' => $loaiPhong->id,
                    'tang' => $phong['tang'],
                    'trang_thai' => $phong['trang_thai'],
                    'tinh_trang_ve_sinh' => $phong['tinh_trang_ve_sinh'],
                    'tinh_trang_hoat_dong' => $phong['tinh_trang_hoat_dong'],
                    'gia_mac_dinh' => $loaiPhong->gia_mot_dem,
                    'ghi_chu' => 'Du lieu test cho man hinh quan ly phong',
                ]
            );
        }
    }
}
