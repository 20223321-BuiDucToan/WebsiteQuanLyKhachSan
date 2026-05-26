<?php

namespace App\Services;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Support\DatPhongKhachDatQuaHan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class XuLyDatPhongKhachHangQuaHanService
{
    public function xuLy(?Carbon $thoiDiem = null): array
    {
        if (! config('booking.auto_process_overdue_customer_bookings', true)) {
            return [
                'tong_duoc_xu_ly' => 0,
                'da_huy' => 0,
                'khong_den' => 0,
            ];
        }

        $thoiDiemXuLy = ($thoiDiem ?? now())->copy();
        $thongKe = [
            'tong_duoc_xu_ly' => 0,
            'da_huy' => 0,
            'khong_den' => 0,
        ];

        $danhSachDatPhong = DatPhong::query()
            ->with([
                'chiTietDatPhong.phong',
                'chiTietDatPhong',
                'suDungDichVu',
                'hoaDon.thanhToan',
            ])
            ->where('nguon_dat', 'website')
            ->whereNull('ngay_nhan_phong_thuc_te')
            ->whereIn('trang_thai', [
                DatPhong::TRANG_THAI_CHO_XAC_NHAN,
                DatPhong::TRANG_THAI_DA_XAC_NHAN,
            ])
            ->whereDate('ngay_nhan_phong_du_kien', '<=', $thoiDiemXuLy->toDateString())
            ->orderBy('id')
            ->get();

        foreach ($danhSachDatPhong as $datPhong) {
            $hanNhanPhong = DatPhongKhachDatQuaHan::layHanXuLy($datPhong);

            if (! $hanNhanPhong || $thoiDiemXuLy->lt($hanNhanPhong)) {
                continue;
            }

            $ketQua = DB::transaction(function () use ($datPhong, $hanNhanPhong) {
                $datPhongHienTai = DatPhong::query()
                    ->with([
                        'chiTietDatPhong.phong',
                        'chiTietDatPhong',
                        'suDungDichVu',
                        'hoaDon.thanhToan',
                    ])
                    ->find($datPhong->id);

                if (! $datPhongHienTai) {
                    return null;
                }

                if ($datPhongHienTai->nguon_dat !== 'website' || $datPhongHienTai->ngay_nhan_phong_thuc_te) {
                    return null;
                }

                if (! in_array($datPhongHienTai->trang_thai, [
                    DatPhong::TRANG_THAI_CHO_XAC_NHAN,
                    DatPhong::TRANG_THAI_DA_XAC_NHAN,
                ], true)) {
                    return null;
                }

                return $this->xuLyMotDatPhong($datPhongHienTai, $hanNhanPhong);
            });

            if (! $ketQua) {
                continue;
            }

            $thongKe['tong_duoc_xu_ly']++;
            $thongKe[$ketQua]++;
        }

        return $thongKe;
    }

    private function xuLyMotDatPhong(DatPhong $datPhong, Carbon $hanNhanPhong): ?string
    {
        $hoaDonDangHoatDong = $this->layHoaDonDangHoatDong($datPhong);
        $trangThaiSauXuLy = DatPhongKhachDatQuaHan::layTrangThaiSauXuLy($datPhong);

        if ($trangThaiSauXuLy === DatPhong::TRANG_THAI_KHONG_DEN) {
            $this->chuyenThanhKhongDen($datPhong, $hanNhanPhong, $hoaDonDangHoatDong);

            return 'khong_den';
        }

        $this->chuyenThanhDaHuy($datPhong, $hanNhanPhong);

        return 'da_huy';
    }

    private function chuyenThanhDaHuy(DatPhong $datPhong, Carbon $hanNhanPhong): void
    {
        $ghiChuTuDong = 'Tự động hủy vì khách đặt online không đến nhận phòng trước '
            . $hanNhanPhong->format('H:i d/m/Y') . '.';

        $datPhong->forceFill([
            'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
            'ghi_chu' => $this->gopGhiChu($datPhong->ghi_chu, $ghiChuTuDong),
        ])->saveQuietly();

        $datPhong->chiTietDatPhong()->update([
            'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
        ]);

        $this->dongBoHoaDonKhiHuyDatPhong($datPhong, $ghiChuTuDong);
        $this->dongBoTrangThaiPhongTheoDatPhong($datPhong->fresh(['chiTietDatPhong.phong']));
    }

    private function chuyenThanhKhongDen(DatPhong $datPhong, Carbon $hanNhanPhong, ?HoaDon $hoaDonDangHoatDong = null): void
    {
        $hoaDonDangHoatDong ??= $this->layHoaDonDangHoatDong($datPhong);
        $soTienDaThu = $hoaDonDangHoatDong ? $hoaDonDangHoatDong->tinhTongTienDaThu() : 0;

        $ghiChuTuDong = 'Tự động chuyển sang không đến vì khách đặt online không đến nhận phòng trước '
            . $hanNhanPhong->format('H:i d/m/Y') . '.';

        $datPhong->forceFill([
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
            'thoi_diem_khong_den' => $hanNhanPhong,
            'phi_khong_den' => $datPhong->tinhPhiKhongDenMacDinh($soTienDaThu),
            'ly_do_khong_den' => $datPhong->ly_do_khong_den ?: 'Tự động ghi nhận không đến do quá hạn nhận phòng của đơn khách đặt online.',
            'ghi_chu' => $this->gopGhiChu($datPhong->ghi_chu, $ghiChuTuDong),
        ])->saveQuietly();

        $datPhong->chiTietDatPhong()->update([
            'trang_thai' => DatPhong::TRANG_THAI_KHONG_DEN,
        ]);

        $this->dongBoTrangThaiPhongTheoDatPhong($datPhong->fresh(['chiTietDatPhong.phong']));
        $this->taoHoacDongBoHoaDonTheoNghiepVu(
            $datPhong->fresh(['hoaDon.thanhToan', 'chiTietDatPhong', 'suDungDichVu']),
            $ghiChuTuDong
        );
    }

    private function layHoaDonDangHoatDong(DatPhong $datPhong): ?HoaDon
    {
        return $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();
    }

    private function dongBoTrangThaiPhongTheoDatPhong(DatPhong $datPhong): void
    {
        foreach ($datPhong->chiTietDatPhong as $chiTiet) {
            if (! $chiTiet->phong) {
                continue;
            }

            $duLieuCapNhatPhong = [];

            if ($datPhong->trang_thai === DatPhong::TRANG_THAI_DA_NHAN_PHONG) {
                $duLieuCapNhatPhong['tinh_trang_ve_sinh'] = 'sach';
            } elseif (
                $datPhong->trang_thai === DatPhong::TRANG_THAI_DA_TRA_PHONG
                || ($datPhong->trang_thai === DatPhong::TRANG_THAI_DA_HUY && $datPhong->ngay_nhan_phong_thuc_te)
            ) {
                $duLieuCapNhatPhong['tinh_trang_ve_sinh'] = 'can_don';
            }

            if ($duLieuCapNhatPhong !== []) {
                $chiTiet->phong->forceFill($duLieuCapNhatPhong)->saveQuietly();
            }

            $chiTiet->phong->refresh()->dongBoTrangThaiHeThong();
        }
    }

    private function taoHoacDongBoHoaDonTheoNghiepVu(DatPhong $datPhong, ?string $ghiChuMacDinh = null): bool
    {
        $datPhong->loadMissing(['hoaDon.thanhToan', 'chiTietDatPhong', 'suDungDichVu']);

        $hoaDonDangHoatDong = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();

        if ($hoaDonDangHoatDong) {
            $hoaDonDangHoatDong->dongBoGiaTriTuDatPhong();
            $this->boSungGhiChuNghiepVuHoaDon($hoaDonDangHoatDong, $ghiChuMacDinh);

            return false;
        }

        $tongTienPhong = $datPhong->tinhTongTienPhongTheoNghiepVu();
        $tongTienDichVu = $datPhong->tinhTongTienDichVuTheoNghiepVu();
        $tongTien = max(0, $tongTienPhong + $tongTienDichVu);
        $trangThaiHoaDon = $tongTien > 0 ? 'chua_thanh_toan' : 'da_thanh_toan';

        $hoaDon = HoaDon::query()->create([
            'ma_hoa_don' => $this->taoMaHoaDon(),
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => $tongTienPhong,
            'tong_tien_dich_vu' => $tongTienDichVu,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => $tongTien,
            'trang_thai' => $trangThaiHoaDon,
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => null,
            'ghi_chu' => $ghiChuMacDinh,
        ]);

        $hoaDon->giaiPhongSauKhiHoanTatThanhToan();

        return true;
    }

    private function boSungGhiChuNghiepVuHoaDon(HoaDon $hoaDon, ?string $ghiChuMacDinh): void
    {
        if (blank($ghiChuMacDinh)) {
            return;
        }

        $ghiChuMoi = $this->gopGhiChu($hoaDon->ghi_chu, $ghiChuMacDinh);

        if ($ghiChuMoi === $hoaDon->ghi_chu) {
            return;
        }

        $hoaDon->forceFill([
            'ghi_chu' => $ghiChuMoi,
        ])->saveQuietly();
    }

    private function dongBoHoaDonKhiHuyDatPhong(DatPhong $datPhong, string $ghiChuTuDong): bool
    {
        $danhSachHoaDonDangHoatDong = $datPhong->hoaDon()
            ->with('thanhToan')
            ->where('trang_thai', '!=', DatPhong::TRANG_THAI_DA_HUY)
            ->get();

        if ($danhSachHoaDonDangHoatDong->isEmpty()) {
            return false;
        }

        foreach ($danhSachHoaDonDangHoatDong as $hoaDon) {
            if ($hoaDon->coThanhToanThanhCong()) {
                continue;
            }

            $hoaDon->forceFill([
                'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
                'ghi_chu' => $this->gopGhiChu($hoaDon->ghi_chu, $ghiChuTuDong),
            ])->saveQuietly();
        }

        return true;
    }

    private function taoMaHoaDon(): string
    {
        do {
            $maHoaDon = 'HD' . now()->format('ymdHis') . random_int(10, 99);
        } while (HoaDon::query()->where('ma_hoa_don', $maHoaDon)->exists());

        return $maHoaDon;
    }

    private function gopGhiChu(?string $ghiChuHienTai, string $ghiChuMoi): string
    {
        $ghiChuHienTai = trim((string) $ghiChuHienTai);
        $ghiChuMoi = trim($ghiChuMoi);

        if ($ghiChuMoi === '' || str_contains($ghiChuHienTai, $ghiChuMoi)) {
            return $ghiChuHienTai;
        }

        return $ghiChuHienTai === ''
            ? $ghiChuMoi
            : $ghiChuHienTai . PHP_EOL . $ghiChuMoi;
    }
}
