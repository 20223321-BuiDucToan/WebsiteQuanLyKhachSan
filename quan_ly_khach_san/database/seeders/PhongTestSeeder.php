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
                'ma_loai_phong' => 'LPECO',
                'ten_loai_phong' => 'Economy',
                'mo_ta' => 'Phòng nhỏ gọn cho khách đi công tác ngắn ngày.',
                'gia_mot_dem' => 420000,
                'so_nguoi_toi_da' => 2,
                'dien_tich' => 20,
                'so_giuong' => 1,
                'loai_giuong' => 'Double',
                'so_phong_tam' => 1,
                'co_ban_cong' => false,
                'co_bep_rieng' => false,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPSTD',
                'ten_loai_phong' => 'Standard',
                'mo_ta' => 'Phòng tiêu chuẩn phù hợp cho khách lẻ và cặp đôi.',
                'gia_mot_dem' => 560000,
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
                'ma_loai_phong' => 'LPTWN',
                'ten_loai_phong' => 'Standard Twin',
                'mo_ta' => 'Phòng hai giường đơn cho khách đi cùng đồng nghiệp hoặc bạn bè.',
                'gia_mot_dem' => 620000,
                'so_nguoi_toi_da' => 2,
                'dien_tich' => 27,
                'so_giuong' => 2,
                'loai_giuong' => 'Twin',
                'so_phong_tam' => 1,
                'co_ban_cong' => false,
                'co_bep_rieng' => false,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPDLG',
                'ten_loai_phong' => 'Deluxe Garden',
                'mo_ta' => 'Phòng rộng, có cửa sổ lớn nhìn ra khu vườn.',
                'gia_mot_dem' => 790000,
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
                'ma_loai_phong' => 'LPDLO',
                'ten_loai_phong' => 'Deluxe Ocean',
                'mo_ta' => 'Phòng cao cấp có ban công và tầm nhìn biển.',
                'gia_mot_dem' => 980000,
                'so_nguoi_toi_da' => 2,
                'dien_tich' => 36,
                'so_giuong' => 1,
                'loai_giuong' => 'King',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => false,
                'co_huong_bien' => true,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPPMC',
                'ten_loai_phong' => 'Premium City',
                'mo_ta' => 'Phòng premium hướng thành phố, diện tích rộng và nội thất mới.',
                'gia_mot_dem' => 1250000,
                'so_nguoi_toi_da' => 3,
                'dien_tich' => 44,
                'so_giuong' => 1,
                'loai_giuong' => 'King + Sofa',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => false,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPFAM',
                'ten_loai_phong' => 'Family Suite',
                'mo_ta' => 'Phòng gia đình có không gian sinh hoạt và bếp nhỏ.',
                'gia_mot_dem' => 1680000,
                'so_nguoi_toi_da' => 4,
                'dien_tich' => 55,
                'so_giuong' => 2,
                'loai_giuong' => 'Queen + Queen',
                'so_phong_tam' => 1,
                'co_ban_cong' => true,
                'co_bep_rieng' => true,
                'co_huong_bien' => false,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPEXS',
                'ten_loai_phong' => 'Executive Suite',
                'mo_ta' => 'Suite cao cấp cho khách doanh nhân và khách VIP.',
                'gia_mot_dem' => 2380000,
                'so_nguoi_toi_da' => 4,
                'dien_tich' => 68,
                'so_giuong' => 2,
                'loai_giuong' => 'King + Sofa bed',
                'so_phong_tam' => 2,
                'co_ban_cong' => true,
                'co_bep_rieng' => true,
                'co_huong_bien' => true,
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_loai_phong' => 'LPPRE',
                'ten_loai_phong' => 'Presidential',
                'mo_ta' => 'Hạng phòng cao nhất với phòng khách riêng và tầm nhìn toàn cảnh.',
                'gia_mot_dem' => 4200000,
                'so_nguoi_toi_da' => 6,
                'dien_tich' => 120,
                'so_giuong' => 3,
                'loai_giuong' => 'King + Queen + Sofa bed',
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
            ['so_phong' => '101', 'ma_loai' => 'LPECO', 'tang' => 1, 'gia_mac_dinh' => 430000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '102', 'ma_loai' => 'LPECO', 'tang' => 1, 'gia_mac_dinh' => 450000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '103', 'ma_loai' => 'LPSTD', 'tang' => 1, 'gia_mac_dinh' => 560000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '104', 'ma_loai' => 'LPSTD', 'tang' => 1, 'gia_mac_dinh' => 590000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '105', 'ma_loai' => 'LPTWN', 'tang' => 1, 'gia_mac_dinh' => 620000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '106', 'ma_loai' => 'LPTWN', 'tang' => 1, 'gia_mac_dinh' => 640000, 've_sinh' => 'can_don', 'hoat_dong' => 'tam_ngung'],
            ['so_phong' => '201', 'ma_loai' => 'LPDLG', 'tang' => 2, 'gia_mac_dinh' => 790000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '202', 'ma_loai' => 'LPDLG', 'tang' => 2, 'gia_mac_dinh' => 820000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '203', 'ma_loai' => 'LPDLO', 'tang' => 2, 'gia_mac_dinh' => 980000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '204', 'ma_loai' => 'LPDLO', 'tang' => 2, 'gia_mac_dinh' => 1050000, 've_sinh' => 'dang_don', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '205', 'ma_loai' => 'LPPMC', 'tang' => 2, 'gia_mac_dinh' => 1220000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '206', 'ma_loai' => 'LPPMC', 'tang' => 2, 'gia_mac_dinh' => 1280000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '301', 'ma_loai' => 'LPFAM', 'tang' => 3, 'gia_mac_dinh' => 1650000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '302', 'ma_loai' => 'LPFAM', 'tang' => 3, 'gia_mac_dinh' => 1720000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '303', 'ma_loai' => 'LPEXS', 'tang' => 3, 'gia_mac_dinh' => 2380000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '304', 'ma_loai' => 'LPEXS', 'tang' => 3, 'gia_mac_dinh' => 2450000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '305', 'ma_loai' => 'LPPMC', 'tang' => 3, 'gia_mac_dinh' => 1350000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '401', 'ma_loai' => 'LPSTD', 'tang' => 4, 'gia_mac_dinh' => 610000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '402', 'ma_loai' => 'LPDLG', 'tang' => 4, 'gia_mac_dinh' => 860000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '403', 'ma_loai' => 'LPDLO', 'tang' => 4, 'gia_mac_dinh' => 1120000, 've_sinh' => 'can_don', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '404', 'ma_loai' => 'LPFAM', 'tang' => 4, 'gia_mac_dinh' => 1780000, 've_sinh' => 'can_don', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '405', 'ma_loai' => 'LPEXS', 'tang' => 4, 'gia_mac_dinh' => 2520000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '406', 'ma_loai' => 'LPPRE', 'tang' => 4, 'gia_mac_dinh' => 4200000, 've_sinh' => 'can_don', 'hoat_dong' => 'tam_ngung'],
            ['so_phong' => '501', 'ma_loai' => 'LPPMC', 'tang' => 5, 'gia_mac_dinh' => 1450000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '502', 'ma_loai' => 'LPEXS', 'tang' => 5, 'gia_mac_dinh' => 2650000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
            ['so_phong' => '503', 'ma_loai' => 'LPPRE', 'tang' => 5, 'gia_mac_dinh' => 4650000, 've_sinh' => 'sach', 'hoat_dong' => 'hoat_dong'],
        ];

        foreach ($danhSachPhong as $phong) {
            $loaiPhong = $loaiPhongDaTao[$phong['ma_loai']];

            Phong::query()->updateOrCreate(
                ['so_phong' => $phong['so_phong']],
                [
                    'ma_phong' => 'PH' . $phong['so_phong'],
                    'loai_phong_id' => $loaiPhong->id,
                    'tang' => $phong['tang'],
                    'trang_thai' => $phong['hoat_dong'] === 'tam_ngung' ? 'bao_tri' : 'trong',
                    'tinh_trang_ve_sinh' => $phong['ve_sinh'],
                    'tinh_trang_hoat_dong' => $phong['hoat_dong'],
                    'gia_mac_dinh' => $phong['gia_mac_dinh'],
                    'ghi_chu' => 'Phòng mẫu để kiểm tra giá đa dạng và trạng thái vận hành.',
                    'anh_phong' => null,
                ]
            );
        }
    }
}
