<?php

namespace App\Support;

use App\Models\DatPhong;
use App\Models\HoaDon;
use Carbon\Carbon;

class DatPhongKhachDatQuaHan
{
    public static function coApDung(DatPhong $datPhong): bool
    {
        return config('booking.auto_process_overdue_customer_bookings', true)
            && $datPhong->nguon_dat === 'website'
            && ! $datPhong->ngay_nhan_phong_thuc_te
            && in_array((string) $datPhong->trang_thai, [
                DatPhong::TRANG_THAI_CHO_XAC_NHAN,
                DatPhong::TRANG_THAI_DA_XAC_NHAN,
            ], true);
    }

    public static function layHanXuLy(DatPhong $datPhong): ?Carbon
    {
        if (! self::coApDung($datPhong) || ! $datPhong->ngay_nhan_phong_du_kien) {
            return null;
        }

        $thoiGianCauHinh = (string) config('booking.customer_arrival_deadline', '23:00');
        $thoiGian = preg_match('/^\d{2}:\d{2}$/', $thoiGianCauHinh) ? $thoiGianCauHinh : '23:00';
        [$gio, $phut] = array_map('intval', explode(':', $thoiGian));

        return $datPhong->ngay_nhan_phong_du_kien
            ->copy()
            ->setTime($gio, $phut);
    }

    public static function layTrangThaiSauXuLy(DatPhong $datPhong): ?string
    {
        if (! self::coApDung($datPhong)) {
            return null;
        }

        if ($datPhong->trang_thai === DatPhong::TRANG_THAI_DA_XAC_NHAN) {
            return DatPhong::TRANG_THAI_KHONG_DEN;
        }

        $hoaDonDangHoatDong = self::layHoaDonDangHoatDong($datPhong);

        if ($hoaDonDangHoatDong && $hoaDonDangHoatDong->coThanhToanThanhCong()) {
            return DatPhong::TRANG_THAI_KHONG_DEN;
        }

        return DatPhong::TRANG_THAI_DA_HUY;
    }

    public static function taoChiSoHienThi(DatPhong $datPhong, ?Carbon $thoiDiem = null): array
    {
        $coApDung = self::coApDung($datPhong);
        $hanXuLy = self::layHanXuLy($datPhong);
        $trangThaiSauXuLy = self::layTrangThaiSauXuLy($datPhong);
        $thoiDiemThamChieu = ($thoiDiem ?? now())->copy();
        $soPhutCanhBao = max(1, (int) config('booking.customer_arrival_warning_minutes', 180));
        $soPhutConLai = $hanXuLy
            ? $thoiDiemThamChieu->diffInMinutes($hanXuLy, false)
            : null;
        $daQuaHan = $coApDung && $hanXuLy && $thoiDiemThamChieu->gte($hanXuLy);
        $sapXuLy = $coApDung
            && $hanXuLy
            && ! $daQuaHan
            && $soPhutConLai !== null
            && $soPhutConLai <= $soPhutCanhBao;
        $hanhDong = match ($trangThaiSauXuLy) {
            DatPhong::TRANG_THAI_DA_HUY => 'Tự hủy',
            DatPhong::TRANG_THAI_KHONG_DEN => 'Chuyển không đến',
            default => null,
        };

        return [
            'co_tu_dong_xu_ly_khach_dat' => $coApDung,
            'han_tu_dong_xu_ly' => $hanXuLy,
            'trang_thai_sau_tu_dong_xu_ly' => $trangThaiSauXuLy,
            'hanh_dong_tu_dong_xu_ly' => $hanhDong,
            'sap_tu_dong_xu_ly' => $sapXuLy,
            'da_qua_han_tu_dong_xu_ly' => $daQuaHan,
            'mo_ta_thoi_gian_tu_dong_xu_ly' => self::taoMoTaThoiGian($coApDung, $hanXuLy, $soPhutConLai, $daQuaHan),
        ];
    }

    private static function taoMoTaThoiGian(bool $coApDung, ?Carbon $hanXuLy, ?int $soPhutConLai, bool $daQuaHan): ?string
    {
        if (! $coApDung || ! $hanXuLy) {
            return null;
        }

        if ($daQuaHan) {
            return 'Đã quá hạn, hệ thống sẽ tự xử lý trong ít phút tới.';
        }

        if ($soPhutConLai === null) {
            return null;
        }

        return 'Còn ' . self::dinhDangKhoangThoiGian($soPhutConLai) . ' nữa.';
    }

    private static function dinhDangKhoangThoiGian(int $tongSoPhut): string
    {
        $tongSoPhut = max(0, $tongSoPhut);
        $soNgay = intdiv($tongSoPhut, 1440);
        $soGio = intdiv($tongSoPhut % 1440, 60);
        $soPhut = $tongSoPhut % 60;
        $danhSachPhan = [];

        if ($soNgay > 0) {
            $danhSachPhan[] = $soNgay . ' ngày';
        }

        if ($soGio > 0) {
            $danhSachPhan[] = $soGio . ' giờ';
        }

        if ($soPhut > 0 || $danhSachPhan === []) {
            $danhSachPhan[] = $soPhut . ' phút';
        }

        return implode(' ', array_slice($danhSachPhan, 0, 2));
    }

    private static function layHoaDonDangHoatDong(DatPhong $datPhong): ?HoaDon
    {
        if ($datPhong->relationLoaded('hoaDon')) {
            return $datPhong->hoaDon
                ->where('trang_thai', '!=', 'da_huy')
                ->sortByDesc('id')
                ->first();
        }

        return $datPhong->hoaDon()
            ->with('thanhToan')
            ->where('trang_thai', '!=', 'da_huy')
            ->latest('id')
            ->first();
    }
}
