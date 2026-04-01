<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\Phong;
use App\Models\ThanhToan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class BaoCaoThongKeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuNgay = $request->filled('tu_ngay')
            ? Carbon::parse($request->input('tu_ngay'))->startOfDay()
            : now()->startOfMonth();
        $denNgay = $request->filled('den_ngay')
            ? Carbon::parse($request->input('den_ngay'))->endOfDay()
            : now()->endOfDay();

        $tongDatPhong = DatPhong::query()
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->count();

        $datPhongThanhCong = DatPhong::query()
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->where('trang_thai', 'da_tra_phong')
            ->count();

        $datPhongDaHuy = DatPhong::query()
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->where('trang_thai', 'da_huy')
            ->count();

        $tongDoanhThuHoaDon = (float) HoaDon::query()
            ->whereBetween('thoi_diem_xuat', [$tuNgay, $denNgay])
            ->where('trang_thai', '!=', 'da_huy')
            ->sum('tong_tien');

        $tongDaThu = (float) ThanhToan::query()
            ->whereBetween('thoi_diem_thanh_toan', [$tuNgay, $denNgay])
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');

        $congNo = max(0, $tongDoanhThuHoaDon - $tongDaThu);
        $tyLeHuy = $tongDatPhong > 0 ? round(($datPhongDaHuy / $tongDatPhong) * 100, 2) : 0;

        $tongPhong = Phong::query()->count();
        $soNgayBaoCao = max(1, $tuNgay->copy()->startOfDay()->diffInDays($denNgay->copy()->startOfDay()) + 1);
        $tongDemKhaDung = $tongPhong * $soNgayBaoCao;
        $tongDemDaDat = $this->tinhTongDemDaDat($tuNgay, $denNgay);
        $congSuatPhong = $tongDemKhaDung > 0 ? round(($tongDemDaDat / $tongDemKhaDung) * 100, 2) : 0;

        [$nhanDoanhThu, $duLieuDoanhThu] = $this->duLieuDoanhThu6Thang();
        [$nhanDatPhong, $duLieuDatPhong] = $this->duLieuDatPhong6Thang();

        $topPhong = ChiTietDatPhong::query()
            ->selectRaw('phong_id, COUNT(*) as so_luot, SUM(so_dem) as tong_so_dem')
            ->whereHas('datPhong', function ($query) use ($tuNgay, $denNgay) {
                $query
                    ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
                    ->whereIn('trang_thai', ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong']);
            })
            ->groupBy('phong_id')
            ->orderByDesc('so_luot')
            ->with('phong')
            ->take(5)
            ->get();

        return view('bao_cao.index', [
            'tuNgay' => $tuNgay->toDateString(),
            'denNgay' => $denNgay->toDateString(),
            'tongDatPhong' => $tongDatPhong,
            'datPhongThanhCong' => $datPhongThanhCong,
            'datPhongDaHuy' => $datPhongDaHuy,
            'tongDoanhThuHoaDon' => $tongDoanhThuHoaDon,
            'tongDaThu' => $tongDaThu,
            'congNo' => $congNo,
            'tyLeHuy' => $tyLeHuy,
            'tongPhong' => $tongPhong,
            'tongDemDaDat' => $tongDemDaDat,
            'congSuatPhong' => $congSuatPhong,
            'nhanDoanhThu' => $nhanDoanhThu,
            'duLieuDoanhThu' => $duLieuDoanhThu,
            'nhanDatPhong' => $nhanDatPhong,
            'duLieuDatPhong' => $duLieuDatPhong,
            'topPhong' => $topPhong,
        ]);
    }

    private function tinhTongDemDaDat(Carbon $tuNgay, Carbon $denNgay): int
    {
        $tuNgayLoc = $tuNgay->copy()->startOfDay();
        $denNgayLoc = $denNgay->copy()->addDay()->startOfDay();

        $danhSachChiTiet = ChiTietDatPhong::query()
            ->with('datPhong:id,ngay_nhan_phong_du_kien,ngay_tra_phong_du_kien,trang_thai')
            ->whereHas('datPhong', function ($query) use ($tuNgayLoc, $denNgayLoc) {
                $query
                    ->whereIn('trang_thai', ['da_nhan_phong', 'da_tra_phong'])
                    ->whereDate('ngay_nhan_phong_du_kien', '<', $denNgayLoc->toDateString())
                    ->whereDate('ngay_tra_phong_du_kien', '>', $tuNgayLoc->toDateString());
            })
            ->get();

        $tongDemDaDat = 0;

        foreach ($danhSachChiTiet as $chiTiet) {
            if (!$chiTiet->datPhong) {
                continue;
            }

            $batDau = Carbon::parse($chiTiet->datPhong->ngay_nhan_phong_du_kien)->startOfDay();
            $ketThuc = Carbon::parse($chiTiet->datPhong->ngay_tra_phong_du_kien)->startOfDay();

            $mocBatDau = $batDau->greaterThan($tuNgayLoc) ? $batDau : $tuNgayLoc;
            $mocKetThuc = $ketThuc->lessThan($denNgayLoc) ? $ketThuc : $denNgayLoc;

            $soDemGiaoNhau = max(0, $mocBatDau->diffInDays($mocKetThuc, false));
            $tongDemDaDat += $soDemGiaoNhau;
        }

        return $tongDemDaDat;
    }

    private function duLieuDoanhThu6Thang(): array
    {
        $thangBatDau = now()->startOfMonth()->subMonths(5);
        $thangKetThuc = now()->endOfMonth();

        $danhSachHoaDon = HoaDon::query()
            ->whereBetween('thoi_diem_xuat', [$thangBatDau, $thangKetThuc])
            ->where('trang_thai', '!=', 'da_huy')
            ->get(['thoi_diem_xuat', 'tong_tien']);

        $nhan = [];
        $duLieu = [];

        foreach (CarbonPeriod::create($thangBatDau, '1 month', now()->startOfMonth()) as $thang) {
            $key = $thang->format('Y-m');
            $nhan[] = $thang->format('m/Y');
            $duLieu[] = (float) $danhSachHoaDon
                ->filter(fn($hoaDon) => optional($hoaDon->thoi_diem_xuat)->format('Y-m') === $key)
                ->sum('tong_tien');
        }

        return [$nhan, $duLieu];
    }

    private function duLieuDatPhong6Thang(): array
    {
        $thangBatDau = now()->startOfMonth()->subMonths(5);
        $thangKetThuc = now()->endOfMonth();

        $danhSachDatPhong = DatPhong::query()
            ->whereBetween('ngay_dat', [$thangBatDau, $thangKetThuc])
            ->get(['ngay_dat']);

        $nhan = [];
        $duLieu = [];

        foreach (CarbonPeriod::create($thangBatDau, '1 month', now()->startOfMonth()) as $thang) {
            $key = $thang->format('Y-m');
            $nhan[] = $thang->format('m/Y');
            $duLieu[] = (int) $danhSachDatPhong
                ->filter(fn($datPhong) => optional($datPhong->ngay_dat)->format('Y-m') === $key)
                ->count();
        }

        return [$nhan, $duLieu];
    }
}
