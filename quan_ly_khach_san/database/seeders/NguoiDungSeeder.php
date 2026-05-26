<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
use Illuminate\Database\Seeder;

class NguoiDungSeeder extends Seeder
{
    public function run(): void
    {
        $danhSachNguoiDung = [
            [
                'ten_dang_nhap' => 'admin',
                'ho_ten' => 'Quản trị hệ thống',
                'email' => 'admin@gmail.com',
                'password' => '123456',
                'so_dien_thoai' => '0900000000',
                'dia_chi' => 'Văn phòng điều hành khách sạn',
                'vai_tro' => 'admin',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subMinutes(20),
            ],
            [
                'ten_dang_nhap' => 'nhanvien1',
                'ho_ten' => 'Lê Thanh Thảo',
                'email' => 'nhanvien1@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0901000001',
                'dia_chi' => 'Ca sáng - lễ tân',
                'vai_tro' => 'nhan_vien',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subHours(2),
            ],
            [
                'ten_dang_nhap' => 'nhanvien2',
                'ho_ten' => 'Trần Minh Phúc',
                'email' => 'nhanvien2@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0901000002',
                'dia_chi' => 'Ca chiều - vận hành',
                'vai_tro' => 'nhan_vien',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subHours(4),
            ],
            [
                'ten_dang_nhap' => 'khach1',
                'ho_ten' => 'Nguyễn Hà An',
                'email' => 'khach1@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0911000001',
                'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
                'vai_tro' => 'khach_hang',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subDays(1),
            ],
            [
                'ten_dang_nhap' => 'khach2',
                'ho_ten' => 'Phạm Minh Thư',
                'email' => 'khach2@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0911000002',
                'dia_chi' => 'Hải Châu, Đà Nẵng',
                'vai_tro' => 'khach_hang',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subDays(2),
            ],
            [
                'ten_dang_nhap' => 'khach3',
                'ho_ten' => 'Võ Đức Huy',
                'email' => 'khach3@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0911000003',
                'dia_chi' => 'Nha Trang, Khánh Hòa',
                'vai_tro' => 'khach_hang',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subDays(3),
            ],
            [
                'ten_dang_nhap' => 'khach4',
                'ho_ten' => 'Bùi Khánh Linh',
                'email' => 'khach4@example.com',
                'password' => '123456',
                'so_dien_thoai' => '0911000004',
                'dia_chi' => 'Biên Hòa, Đồng Nai',
                'vai_tro' => 'khach_hang',
                'trang_thai' => 'hoat_dong',
                'lan_dang_nhap_cuoi' => now()->subDays(4),
            ],
        ];

        foreach ($danhSachNguoiDung as $nguoiDung) {
            NguoiDung::query()->updateOrCreate(
                ['ten_dang_nhap' => $nguoiDung['ten_dang_nhap']],
                $nguoiDung
            );
        }
    }
}
