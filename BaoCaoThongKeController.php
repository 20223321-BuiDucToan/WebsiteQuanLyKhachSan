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
use Illuminate\Support\Collection;

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

        $soNgayBaoCao = max(1, $tuNgay->copy()->startOfDay()->diffInDays($denNgay->copy()->startOfDay()) + 1);

        $danhSachDatPhong = DatPhong::query()
            ->with('khachHang')
            ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
            ->get();

        $danhSachHoaDonTrongKy = HoaDon::query()
            ->with(['datPhong.khachHang', 'thanhToan'])
            ->whereBetween('thoi_diem_xuat', [$tuNgay, $denNgay])
            ->get()
            ->map(fn(HoaDon $hoaDon) => $this->boSungDuLieuHoaDon($hoaDon));

        $danhSachHoaDonHopLe = $danhSachHoaDonTrongKy
            ->where('trang_thai_hien_thi', '!=', 'da_huy')
            ->values();

        $danhSachThanhToanThanhCongTrongKy = ThanhToan::query()
            ->whereBetween('thoi_diem_thanh_toan', [$tuNgay, $denNgay])
            ->where('trang_thai', 'thanh_cong')
            ->get();

        $tongDatPhong = $danhSachDatPhong->count();
        $datPhongThanhCong = $danhSachDatPhong->where('trang_thai', 'da_tra_phong')->count();
        $datPhongDangXuLy = $danhSachDatPhong
            ->filter(fn(DatPhong $datPhong) => in_array($datPhong->trang_thai, ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'], true))
            ->count();
        $datPhongDaHuy = $danhSachDatPhong->where('trang_thai', 'da_huy')->count();
        $tyLeHuy = $tongDatPhong > 0 ? round(($datPhongDaHuy / $tongDatPhong) * 100, 2) : 0;
        $tyLeHoanTatDatPhong = $tongDatPhong > 0 ? round(($datPhongThanhCong / $tongDatPhong) * 100, 2) : 0;

        $tongHoaDon = $danhSachHoaDonHopLe->count();
        $tongDoanhThuHoaDon = (float) $danhSachHoaDonHopLe->sum('tong_tien');
        $tongThuHoiHoaDonTrongKy = (float) $danhSachHoaDonHopLe->sum(function (HoaDon $hoaDon) {
            return min((float) $hoaDon->tong_tien, (float) $hoaDon->so_tien_da_thu);
        });
        $congNo = (float) $danhSachHoaDonHopLe->sum('so_tien_con_lai');
        $dongTienThuTrongKy = (float) $danhSachThanhToanThanhCongTrongKy->sum('so_tien');
        $tyLeThuHoi = $tongDoanhThuHoaDon > 0 ? round(($tongThuHoiHoaDonTrongKy / $tongDoanhThuHoaDon) * 100, 2) : 0;
        $giaTriHoaDonTrungBinh = $tongHoaDon > 0 ? round($tongDoanhThuHoaDon / $tongHoaDon, 0) : 0;

        $tongTienPhong = (float) $danhSachHoaDonHopLe->sum('tong_tien_phong');
        $tongTienDichVu = (float) $danhSachHoaDonHopLe->sum('tong_tien_dich_vu');
        $tongGiamGia = (float) $danhSachHoaDonHopLe->sum('giam_gia');
        $tongThue = (float) $danhSachHoaDonHopLe->sum('thue');

        $tongPhong = Phong::query()->count();
        $tongDemKhaDung = $tongPhong * $soNgayBaoCao;
        $tongDemDaDat = $this->tinhTongDemDaDat($tuNgay, $denNgay);
        $congSuatPhong = $tongDemKhaDung > 0 ? round(($tongDemDaDat / $tongDemKhaDung) * 100, 2) : 0;

        [$tuNgayKyTruoc, $denNgayKyTruoc] = $this->layKySoSanh($tuNgay, $denNgay);
        $tongDoanhThuKyTruoc = (float) HoaDon::query()
            ->whereBetween('thoi_diem_xuat', [$tuNgayKyTruoc, $denNgayKyTruoc])
            ->where('trang_thai', '!=', 'da_huy')
            ->sum('tong_tien');
        $tongDatPhongKyTruoc = DatPhong::query()
            ->whereBetween('ngay_dat', [$tuNgayKyTruoc, $denNgayKyTruoc])
            ->count();
        $dongTienKyTruoc = (float) ThanhToan::query()
            ->whereBetween('thoi_diem_thanh_toan', [$tuNgayKyTruoc, $denNgayKyTruoc])
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');

        $bienDongDoanhThu = $this->tinhBienDongPhanTram($tongDoanhThuHoaDon, $tongDoanhThuKyTruoc);
        $bienDongDatPhong = $this->tinhBienDongPhanTram((float) $tongDatPhong, (float) $tongDatPhongKyTruoc);
        $bienDongDongTien = $this->tinhBienDongPhanTram($dongTienThuTrongKy, $dongTienKyTruoc);

        $cotMocThoiGian = $this->taoCotMocThoiGian($tuNgay, $denNgay);

        $duLieuDoanhThu = $this->tongHopTheoCotMoc(
            $danhSachHoaDonHopLe,
            fn(HoaDon $hoaDon) => $hoaDon->thoi_diem_xuat,
            fn(HoaDon $hoaDon) => (float) $hoaDon->tong_tien,
            $cotMocThoiGian
        );

        $duLieuThuTien = $this->tongHopTheoCotMoc(
            $danhSachThanhToanThanhCongTrongKy,
            fn(ThanhToan $thanhToan) => $thanhToan->thoi_diem_thanh_toan,
            fn(ThanhToan $thanhToan) => (float) $thanhToan->so_tien,
            $cotMocThoiGian
        );

        $duLieuDatPhong = $this->tongHopTheoCotMoc(
            $danhSachDatPhong,
            fn(DatPhong $datPhong) => $datPhong->ngay_dat,
            fn() => 1,
            $cotMocThoiGian
        );

        $duLieuDatHuy = $this->tongHopTheoCotMoc(
            $danhSachDatPhong->where('trang_thai', 'da_huy')->values(),
            fn(DatPhong $datPhong) => $datPhong->ngay_dat,
            fn() => 1,
            $cotMocThoiGian
        );

        $phanBoNguonDat = $this->tongHopTheoDanhMuc(
            $danhSachDatPhong,
            fn(DatPhong $datPhong) => (string) ($datPhong->nguon_dat ?? 'khac'),
            ['website', 'truc_tiep', 'dien_thoai', 'zalo', 'khac']
        );

        $phanBoThanhToan = $this->tongHopTheoDanhMuc(
            $danhSachThanhToanThanhCongTrongKy,
            fn(ThanhToan $thanhToan) => (string) ($thanhToan->phuong_thuc_thanh_toan ?? 'khac'),
            ['tien_mat', 'chuyen_khoan', 'the', 'vi_dien_tu', 'khac']
        );

        $phanBoTrangThaiDatPhong = $this->tongHopTheoDanhMuc(
            $danhSachDatPhong,
            fn(DatPhong $datPhong) => (string) $datPhong->trang_thai,
            ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong', 'da_tra_phong', 'da_huy']
        );

        $phanBoTrangThaiHoaDon = $this->tongHopTheoDanhMuc(
            $danhSachHoaDonTrongKy,
            fn(HoaDon $hoaDon) => (string) $hoaDon->trang_thai_hien_thi,
            ['chua_thanh_toan', 'thanh_toan_mot_phan', 'da_thanh_toan', 'da_huy']
        );

        $topPhong = ChiTietDatPhong::query()
            ->selectRaw('phong_id, COUNT(*) as so_luot, SUM(so_dem) as tong_so_dem, SUM(gia_phong * so_dem) as doanh_thu_phong')
            ->whereHas('datPhong', function ($query) use ($tuNgay, $denNgay) {
                $query
                    ->whereBetween('ngay_dat', [$tuNgay, $denNgay])
                    ->whereIn('trang_thai', ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong']);
            })
            ->groupBy('phong_id')
            ->orderByDesc('tong_so_dem')
            ->with('phong')
            ->take(5)
            ->get();

        $topKhachHang = $danhSachHoaDonHopLe
            ->filter(fn(HoaDon $hoaDon) => $hoaDon->datPhong?->khachHang)
            ->groupBy(fn(HoaDon $hoaDon) => $hoaDon->datPhong->khachHang->id)
            ->map(function (Collection $hoaDons) {
                $khachHang = $hoaDons->first()->datPhong->khachHang;

                return (object) [
                    'ho_ten' => $khachHang->ho_ten,
                    'so_dien_thoai' => $khachHang->so_dien_thoai,
                    'so_hoa_don' => $hoaDons->count(),
                    'doanh_thu' => (float) $hoaDons->sum('tong_tien'),
                    'da_thu' => (float) $hoaDons->sum(function (HoaDon $hoaDon) {
                        return min((float) $hoaDon->tong_tien, (float) $hoaDon->so_tien_da_thu);
                    }),
                ];
            })
            ->sortByDesc('doanh_thu')
            ->take(5)
            ->values();

        $insights = $this->taoInsightBaoCao(
            $congSuatPhong,
            $tyLeHuy,
            $bienDongDoanhThu,
            $tyLeThuHoi,
            $phanBoNguonDat,
            $phanBoThanhToan
        );

        return view('bao_cao.index', [
            'tuNgay' => $tuNgay->toDateString(),
            'denNgay' => $denNgay->toDateString(),
            'soNgayBaoCao' => $soNgayBaoCao,
            'tongDatPhong' => $tongDatPhong,
            'datPhongThanhCong' => $datPhongThanhCong,
            'datPhongDangXuLy' => $datPhongDangXuLy,
            'datPhongDaHuy' => $datPhongDaHuy,
            'tyLeHuy' => $tyLeHuy,
            'tyLeHoanTatDatPhong' => $tyLeHoanTatDatPhong,
            'tongHoaDon' => $tongHoaDon,
            'tongDoanhThuHoaDon' => $tongDoanhThuHoaDon,
            'tongThuHoiHoaDonTrongKy' => $tongThuHoiHoaDonTrongKy,
            'dongTienThuTrongKy' => $dongTienThuTrongKy,
            'congNo' => $congNo,
            'tyLeThuHoi' => $tyLeThuHoi,
            'giaTriHoaDonTrungBinh' => $giaTriHoaDonTrungBinh,
            'tongTienPhong' => $tongTienPhong,
            'tongTienDichVu' => $tongTienDichVu,
            'tongGiamGia' => $tongGiamGia,
            'tongThue' => $tongThue,
            'tongPhong' => $tongPhong,
            'tongDemDaDat' => $tongDemDaDat,
            'tongDemKhaDung' => $tongDemKhaDung,
            'congSuatPhong' => $congSuatPhong,
            'nhanXuHuong' => $cotMocThoiGian['labels'],
            'moTaCotMoc' => $cotMocThoiGian['mo_ta'],
            'duLieuDoanhThu' => $duLieuDoanhThu,
            'duLieuThuTien' => $duLieuThuTien,
            'duLieuDatPhong' => $duLieuDatPhong,
            'duLieuDatHuy' => $duLieuDatHuy,
            'phanBoNguonDat' => $phanBoNguonDat,
            'phanBoThanhToan' => $phanBoThanhToan,
            'phanBoTrangThaiDatPhong' => $phanBoTrangThaiDatPhong,
            'phanBoTrangThaiHoaDon' => $phanBoTrangThaiHoaDon,
            'topPhong' => $topPhong,
            'topKhachHang' => $topKhachHang,
            'insights' => $insights,
            'bienDongDoanhThu' => $bienDongDoanhThu,
            'bienDongDatPhong' => $bienDongDatPhong,
            'bienDongDongTien' => $bienDongDongTien,
        ]);
    }

    private function boSungDuLieuHoaDon(HoaDon $hoaDon): HoaDon
    {
        $soTienDaThu = (float) $hoaDon->thanhToan
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');

        $tongTien = (float) $hoaDon->tong_tien;
        $soTienConLai = max(0, $tongTien - $soTienDaThu);
        $trangThaiHienThi = $this->xacDinhTrangThaiHoaDon($hoaDon, $soTienDaThu);

        $hoaDon->setAttribute('so_tien_da_thu', $soTienDaThu);
        $hoaDon->setAttribute('so_tien_con_lai', $soTienConLai);
        $hoaDon->setAttribute('trang_thai_hien_thi', $trangThaiHienThi);

        return $hoaDon;
    }

    private function xacDinhTrangThaiHoaDon(HoaDon $hoaDon, float $soTienDaThu): string
    {
        if ($hoaDon->trang_thai === 'da_huy') {
            return 'da_huy';
        }

        if ($soTienDaThu >= (float) $hoaDon->tong_tien) {
            return 'da_thanh_toan';
        }

        if ($soTienDaThu > 0) {
            return 'thanh_toan_mot_phan';
        }

        return 'chua_thanh_toan';
    }

    private function tinhTongDemDaDat(Carbon $tuNgay, Carbon $denNgay): int
    {
        $tuNgayLoc = $tuNgay->copy()->startOfDay();
        $denNgayLoc = $denNgay->copy()->addDay()->startOfDay();

        $danhSachChiTiet = ChiTietDatPhong::query()
            ->with('datPhong:id,ngay_nhan_phong_du_kien,ngay_tra_phong_du_kien,trang_thai')
            ->whereHas('datPhong', function ($query) use ($tuNgayLoc, $denNgayLoc) {
                $query
                    ->whereIn('trang_thai', ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong'])
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

    private function layKySoSanh(Carbon $tuNgay, Carbon $denNgay): array
    {
        $soNgay = max(1, $tuNgay->copy()->startOfDay()->diffInDays($denNgay->copy()->startOfDay()) + 1);
        $denNgayKyTruoc = $tuNgay->copy()->subDay()->endOfDay();
        $tuNgayKyTruoc = $denNgayKyTruoc->copy()->subDays($soNgay - 1)->startOfDay();

        return [$tuNgayKyTruoc, $denNgayKyTruoc];
    }

    private function tinhBienDongPhanTram(float $giaTriHienTai, float $giaTriKyTruoc): float
    {
        if ($giaTriKyTruoc == 0.0) {
            return $giaTriHienTai > 0 ? 100.0 : 0.0;
        }

        return round((($giaTriHienTai - $giaTriKyTruoc) / $giaTriKyTruoc) * 100, 1);
    }

    private function taoCotMocThoiGian(Carbon $tuNgay, Carbon $denNgay): array
    {
        $soNgay = max(1, $tuNgay->copy()->startOfDay()->diffInDays($denNgay->copy()->startOfDay()) + 1);
        $gomTheoNgay = $soNgay <= 31;

        $batDau = $gomTheoNgay ? $tuNgay->copy()->startOfDay() : $tuNgay->copy()->startOfMonth();
        $ketThuc = $gomTheoNgay ? $denNgay->copy()->startOfDay() : $denNgay->copy()->startOfMonth();
        $buoc = $gomTheoNgay ? '1 day' : '1 month';
        $keyFormat = $gomTheoNgay ? 'Y-m-d' : 'Y-m';
        $labelFormat = $gomTheoNgay ? 'd/m' : 'm/Y';

        $labels = [];
        $keys = [];

        foreach (CarbonPeriod::create($batDau, $buoc, $ketThuc) as $mocThoiGian) {
            $labels[] = $mocThoiGian->format($labelFormat);
            $keys[] = $mocThoiGian->format($keyFormat);
        }

        return [
            'labels' => $labels,
            'keys' => $keys,
            'key_format' => $keyFormat,
            'mo_ta' => $gomTheoNgay ? 'theo ngày' : 'theo tháng',
        ];
    }

    private function tongHopTheoCotMoc(Collection $items, callable $layThoiDiem, callable $layGiaTri, array $cotMoc): array
    {
        $duLieu = array_fill(0, count($cotMoc['keys']), 0);
        $chiSoTheoKey = array_flip($cotMoc['keys']);

        foreach ($items as $item) {
            $thoiDiem = $layThoiDiem($item);

            if (!$thoiDiem) {
                continue;
            }

            $thoiDiem = Carbon::parse($thoiDiem);
            $key = $thoiDiem->format($cotMoc['key_format']);

            if (!array_key_exists($key, $chiSoTheoKey)) {
                continue;
            }

            $duLieu[$chiSoTheoKey[$key]] += (float) $layGiaTri($item);
        }

        return array_map(fn($giaTri) => round($giaTri, 2), $duLieu);
    }

    private function tongHopTheoDanhMuc(Collection $items, callable $layKey, array $danhMuc): array
    {
        $tong = max(1, $items->count());
        $ketQua = [];

        foreach ($danhMuc as $key) {
            $giaTri = $items->filter(fn($item) => (string) $layKey($item) === $key)->count();

            $ketQua[] = [
                'key' => $key,
                'label' => \App\Support\HienThiGiaTri::nhanGiaTri($key),
                'value' => $giaTri,
                'percent' => $items->isNotEmpty() ? round(($giaTri / $tong) * 100, 1) : 0,
            ];
        }

        return $ketQua;
    }

    private function taoInsightBaoCao(
        float $congSuatPhong,
        float $tyLeHuy,
        float $bienDongDoanhThu,
        float $tyLeThuHoi,
        array $phanBoNguonDat,
        array $phanBoThanhToan
    ): array {
        $nguonChuDao = collect($phanBoNguonDat)
            ->sortByDesc('value')
            ->firstWhere('value', '>', 0);

        $phuongThucChuDao = collect($phanBoThanhToan)
            ->sortByDesc('value')
            ->firstWhere('value', '>', 0);

        $insights = [];

        $insights[] = $congSuatPhong >= 80
            ? 'Công suất phòng đang rất tốt, nên ưu tiên chiến lược giữ giá và bán kèm dịch vụ.'
            : ($congSuatPhong >= 55
                ? 'Công suất phòng ở mức ổn định, có thể đẩy thêm gói combo để tăng doanh thu trên mỗi đơn.'
                : 'Công suất phòng còn thấp, nên ưu tiên khuyến mãi ngắn hạn và tối ưu nguồn khách hiệu quả.');

        $insights[] = $tyLeHuy >= 15
            ? 'Tỷ lệ hủy đang cao, nên kiểm tra lại quy trình xác nhận đặt phòng và chính sách giữ chỗ.'
            : 'Tỷ lệ hủy đang được kiểm soát tốt, có thể tập trung vào tối ưu chuyển đổi đặt phòng.';

        $insights[] = $bienDongDoanhThu >= 0
            ? 'Doanh thu phát sinh đang tăng so với kỳ trước, tín hiệu vận hành khá tích cực.'
            : 'Doanh thu phát sinh đang giảm so với kỳ trước, nên rà lại nguồn khách và tốc độ chốt thanh toán.';

        $insights[] = $tyLeThuHoi >= 85
            ? 'Tỷ lệ thu hồi hóa đơn cao, rủi ro công nợ hiện tại ở mức an toàn.'
            : 'Tỷ lệ thu hồi hóa đơn chưa cao, nên ưu tiên nhắc thu các hóa đơn còn mở.';

        if ($nguonChuDao) {
            $insights[] = 'Nguồn đặt chủ đạo hiện tại là ' . mb_strtolower($nguonChuDao['label']) . ', nên tiếp tục đầu tư vào kênh này.';
        }

        if ($phuongThucChuDao) {
            $insights[] = 'Khách đang thanh toán chủ yếu qua ' . mb_strtolower($phuongThucChuDao['label']) . ', phù hợp để tối ưu quy trình đối soát.';
        }

        return array_slice($insights, 0, 4);
    }
}
