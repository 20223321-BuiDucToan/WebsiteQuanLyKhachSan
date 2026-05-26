<?php

namespace Database\Seeders;

use App\Models\DichVu;
use Illuminate\Database\Seeder;

class DichVuDemoSeeder extends Seeder
{
    public function run(): void
    {
        $danhSachDichVu = [
            [
                'ma_dich_vu' => 'DV001',
                'ten_dich_vu' => 'Buffet sáng',
                'loai_dich_vu' => 'Ẩm thực',
                'don_vi_tinh' => 'suất',
                'don_gia' => 160000,
                'mo_ta' => 'Áp dụng cho khách cần thêm bữa sáng ngoài gói phòng.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV002',
                'ten_dich_vu' => 'Minibar',
                'loai_dich_vu' => 'Ẩm thực',
                'don_vi_tinh' => 'món',
                'don_gia' => 55000,
                'mo_ta' => 'Nước uống, snack và đồ dùng nhanh trong phòng.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV003',
                'ten_dich_vu' => 'Giặt ủi nhanh',
                'loai_dich_vu' => 'Tiện ích',
                'don_vi_tinh' => 'bộ',
                'don_gia' => 35000,
                'mo_ta' => 'Trả đồ trong ngày cho khách lưu trú.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV004',
                'ten_dich_vu' => 'Đưa đón sân bay',
                'loai_dich_vu' => 'Vận chuyển',
                'don_vi_tinh' => 'lượt',
                'don_gia' => 320000,
                'mo_ta' => 'Đưa đón sân bay bằng xe 4 hoặc 7 chỗ tùy nhu cầu.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV005',
                'ten_dich_vu' => 'Spa thư giãn',
                'loai_dich_vu' => 'Chăm sóc',
                'don_vi_tinh' => 'lượt',
                'don_gia' => 450000,
                'mo_ta' => 'Liệu trình thư giãn 60 phút tại khu spa.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV006',
                'ten_dich_vu' => 'Giường phụ',
                'loai_dich_vu' => 'Lưu trú',
                'don_vi_tinh' => 'đêm',
                'don_gia' => 280000,
                'mo_ta' => 'Áp dụng cho phòng cần thêm chỗ ngủ.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV007',
                'ten_dich_vu' => 'Trang trí sinh nhật',
                'loai_dich_vu' => 'Sự kiện',
                'don_vi_tinh' => 'gói',
                'don_gia' => 750000,
                'mo_ta' => 'Trang trí bóng bay, bánh nhỏ và thiệp chúc mừng.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV008',
                'ten_dich_vu' => 'Thuê xe theo ngày',
                'loai_dich_vu' => 'Vận chuyển',
                'don_vi_tinh' => 'ngày',
                'don_gia' => 950000,
                'mo_ta' => 'Thuê xe có tài xế cho khách đi công tác hoặc du lịch.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV009',
                'ten_dich_vu' => 'Bữa tối tại phòng',
                'loai_dich_vu' => 'Ẩm thực',
                'don_vi_tinh' => 'suất',
                'don_gia' => 290000,
                'mo_ta' => 'Thực đơn set menu phục vụ tận phòng.',
                'trang_thai' => 'hoat_dong',
            ],
            [
                'ma_dich_vu' => 'DV010',
                'ten_dich_vu' => 'Nước suối thêm',
                'loai_dich_vu' => 'Tiện ích',
                'don_vi_tinh' => 'chai',
                'don_gia' => 20000,
                'mo_ta' => 'Bổ sung nước suối theo nhu cầu trong thời gian lưu trú.',
                'trang_thai' => 'tam_ngung',
            ],
        ];

        foreach ($danhSachDichVu as $dichVu) {
            DichVu::query()->updateOrCreate(
                ['ma_dich_vu' => $dichVu['ma_dich_vu']],
                $dichVu
            );
        }
    }
}
