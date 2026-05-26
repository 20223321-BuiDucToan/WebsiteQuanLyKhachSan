<?php

namespace Database\Seeders;

use App\Models\KhachHang;
use App\Models\NguoiDung;
use Illuminate\Database\Seeder;

class KhachHangDemoSeeder extends Seeder
{
    public function run(): void
    {
        $nguoiDungKhachHang = NguoiDung::query()
            ->whereIn('ten_dang_nhap', ['khach1', 'khach2', 'khach3', 'khach4'])
            ->get()
            ->keyBy('ten_dang_nhap');

        $danhSachKhachHangTheoTaiKhoan = [
            'khach1' => [
                'ma_khach_hang' => 'KH001',
                'ho_ten' => 'Nguyễn Hà An',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => '1998-05-14',
                'so_dien_thoai' => '0911000001',
                'email' => 'khach1@example.com',
                'so_giay_to' => '079098001234',
                'loai_giay_to' => 'cccd',
                'dia_chi' => 'Quận 1, TP. Hồ Chí Minh',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'vang',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Khách thích phòng yên tĩnh và thường thanh toán chuyển khoản.',
            ],
            'khach2' => [
                'ma_khach_hang' => 'KH002',
                'ho_ten' => 'Phạm Minh Thư',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => '1995-11-02',
                'so_dien_thoai' => '0911000002',
                'email' => 'khach2@example.com',
                'so_giay_to' => '048195002468',
                'loai_giay_to' => 'cccd',
                'dia_chi' => 'Hải Châu, Đà Nẵng',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'bac',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Khách hay đặt phòng qua website và cần xuất hóa đơn điện tử.',
            ],
            'khach3' => [
                'ma_khach_hang' => 'KH003',
                'ho_ten' => 'Võ Đức Huy',
                'gioi_tinh' => 'nam',
                'ngay_sinh' => '1992-08-21',
                'so_dien_thoai' => '0911000003',
                'email' => 'khach3@example.com',
                'so_giay_to' => 'C12345678',
                'loai_giay_to' => 'passport',
                'dia_chi' => 'Nha Trang, Khánh Hòa',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'kim_cuong',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Khách thường lưu trú dài ngày và sử dụng nhiều dịch vụ nội khu.',
            ],
            'khach4' => [
                'ma_khach_hang' => 'KH004',
                'ho_ten' => 'Bùi Khánh Linh',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => '2000-01-09',
                'so_dien_thoai' => '0911000004',
                'email' => 'khach4@example.com',
                'so_giay_to' => '079200004321',
                'loai_giay_to' => 'cccd',
                'dia_chi' => 'Biên Hòa, Đồng Nai',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'thuong',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Khách mới, thường hỏi gói phòng gia đình vào cuối tuần.',
            ],
        ];

        foreach ($danhSachKhachHangTheoTaiKhoan as $tenDangNhap => $duLieuKhachHang) {
            $nguoiDung = $nguoiDungKhachHang->get($tenDangNhap);

            if (! $nguoiDung) {
                continue;
            }

            KhachHang::query()->updateOrCreate(
                ['nguoi_dung_id' => $nguoiDung->id],
                $duLieuKhachHang + ['nguoi_dung_id' => $nguoiDung->id]
            );
        }

        $danhSachKhachHangLe = [
            [
                'ma_khach_hang' => 'KH005',
                'ho_ten' => 'Công ty TNHH Sao Biển',
                'gioi_tinh' => null,
                'ngay_sinh' => null,
                'so_dien_thoai' => '02873001234',
                'email' => 'booking@saobien.vn',
                'so_giay_to' => '0312345678',
                'loai_giay_to' => 'khac',
                'dia_chi' => 'Quận 7, TP. Hồ Chí Minh',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'vang',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Khách đoàn doanh nghiệp, thường cần nhiều phòng cùng lúc.',
            ],
            [
                'ma_khach_hang' => 'KH006',
                'ho_ten' => 'Gia đình Trần Quốc Bảo',
                'gioi_tinh' => 'nam',
                'ngay_sinh' => '1988-03-18',
                'so_dien_thoai' => '0906123456',
                'email' => 'quocbao.family@example.com',
                'so_giay_to' => '079188005555',
                'loai_giay_to' => 'cccd',
                'dia_chi' => 'Cần Thơ',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'bac',
                'trang_thai' => 'hoat_dong',
                'ghi_chu' => 'Ưu tiên phòng thông nhau và có thêm giường phụ.',
            ],
            [
                'ma_khach_hang' => 'KH007',
                'ho_ten' => 'Lưu Quỳnh Như',
                'gioi_tinh' => 'nu',
                'ngay_sinh' => '1997-12-05',
                'so_dien_thoai' => '0919777888',
                'email' => 'quynhnhu.walkin@example.com',
                'so_giay_to' => '048197009999',
                'loai_giay_to' => 'cccd',
                'dia_chi' => 'Huế',
                'quoc_tich' => 'Việt Nam',
                'hang_khach_hang' => 'thuong',
                'trang_thai' => 'tam_khoa',
                'ghi_chu' => 'Khách mẫu để kiểm tra bộ lọc trạng thái khách hàng.',
            ],
        ];

        foreach ($danhSachKhachHangLe as $duLieuKhachHang) {
            KhachHang::query()->updateOrCreate(
                ['ma_khach_hang' => $duLieuKhachHang['ma_khach_hang']],
                $duLieuKhachHang
            );
        }
    }
}
