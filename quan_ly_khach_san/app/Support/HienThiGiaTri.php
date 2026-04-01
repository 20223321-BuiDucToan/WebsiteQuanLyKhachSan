<?php

namespace App\Support;

class HienThiGiaTri
{
    public static function nhanGiaTri(?string $giaTri): string
    {
        $duLieu = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_xac_nhan' => 'Đã xác nhận',
            'da_nhan_phong' => 'Đã nhận phòng',
            'da_tra_phong' => 'Đã trả phòng',
            'da_huy' => 'Đã hủy',
            'da_dat' => 'Đã đặt',
            'dang_su_dung' => 'Đang sử dụng',
            'bao_tri' => 'Bảo trì',
            'don_dep' => 'Dọn dẹp',
            'trong' => 'Trống',
            'sach' => 'Sạch',
            'can_don' => 'Cần dọn',
            'dang_don' => 'Đang dọn',
            'ban' => 'Bẩn',
            'hoat_dong' => 'Hoạt động',
            'tam_ngung' => 'Tạm ngưng',
            'tam_khoa' => 'Tạm khóa',
            'chua_thanh_toan' => 'Chưa thanh toán',
            'thanh_toan_mot_phan' => 'Thanh toán một phần',
            'da_thanh_toan' => 'Đã thanh toán',
            'thanh_cong' => 'Thành công',
            'cho_xu_ly' => 'Chờ xử lý',
            'that_bai' => 'Thất bại',
            'truc_tiep' => 'Trực tiếp',
            'dien_thoai' => 'Điện thoại',
            'website' => 'Website',
            'khac' => 'Khác',
            'tien_mat' => 'Tiền mặt',
            'chuyen_khoan' => 'Chuyển khoản',
            'the' => 'Thẻ',
            'vi_dien_tu' => 'Ví điện tử',
            'thuong' => 'Thường',
            'bac' => 'Bạc',
            'vang' => 'Vàng',
            'kim_cuong' => 'Kim cương',
            'admin' => 'Admin',
            'nhan_vien' => 'Nhân viên',
            'khach_hang' => 'Khách hàng',
        ];

        if (empty($giaTri)) {
            return '-';
        }

        return $duLieu[$giaTri] ?? ucwords(str_replace('_', ' ', $giaTri));
    }
}

