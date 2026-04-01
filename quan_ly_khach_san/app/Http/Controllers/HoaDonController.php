<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HoaDonController extends Controller
{
    private const TRANG_THAI = [
        'chua_thanh_toan',
        'thanh_toan_mot_phan',
        'da_thanh_toan',
        'da_huy',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(self::TRANG_THAI)],
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $danhSachHoaDon = HoaDon::query()
            ->with(['datPhong.khachHang', 'datPhong.chiTietDatPhong.phong', 'thanhToan'])
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_hoa_don', 'like', "%{$tuKhoa}%")
                        ->orWhereHas('datPhong', function ($datPhongQuery) use ($tuKhoa) {
                            $datPhongQuery
                                ->where('ma_dat_phong', 'like', "%{$tuKhoa}%")
                                ->orWhereHas('khachHang', function ($khachHangQuery) use ($tuKhoa) {
                                    $khachHangQuery
                                        ->where('ho_ten', 'like', "%{$tuKhoa}%")
                                        ->orWhere('so_dien_thoai', 'like', "%{$tuKhoa}%")
                                        ->orWhere('email', 'like', "%{$tuKhoa}%");
                                });
                        });
                });
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            })
            ->when($tuNgay, function ($query) use ($tuNgay) {
                $query->whereDate('thoi_diem_xuat', '>=', $tuNgay);
            })
            ->when($denNgay, function ($query) use ($denNgay) {
                $query->whereDate('thoi_diem_xuat', '<=', $denNgay);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        foreach ($danhSachHoaDon as $hoaDon) {
            $this->dongBoTrangThaiTheoThanhToan($hoaDon);
        }

        $thongKe = [
            'tong' => HoaDon::query()->count(),
            'chua_thanh_toan' => HoaDon::query()->where('trang_thai', 'chua_thanh_toan')->count(),
            'thanh_toan_mot_phan' => HoaDon::query()->where('trang_thai', 'thanh_toan_mot_phan')->count(),
            'da_thanh_toan' => HoaDon::query()->where('trang_thai', 'da_thanh_toan')->count(),
        ];

        return view('hoa_don.index', compact(
            'danhSachHoaDon',
            'tuKhoa',
            'trangThai',
            'tuNgay',
            'denNgay',
            'thongKe'
        ));
    }

    public function create(Request $request)
    {
        $danhSachDatPhong = DatPhong::query()
            ->with(['khachHang', 'chiTietDatPhong.phong'])
            ->whereIn('trang_thai', ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong'])
            ->whereDoesntHave('hoaDon', function ($query) {
                $query->where('trang_thai', '!=', 'da_huy');
            })
            ->latest('id')
            ->get();

        $datPhongDuocChon = null;
        $tongTienPhong = 0;

        if ($request->filled('dat_phong_id')) {
            $datPhongDuocChon = $danhSachDatPhong->firstWhere('id', (int) $request->input('dat_phong_id'));
            $tongTienPhong = $datPhongDuocChon
                ? $this->tinhTongTienPhongTuDatPhong($datPhongDuocChon)
                : 0;
        }

        return view('hoa_don.create', [
            'danhSachDatPhong' => $danhSachDatPhong,
            'datPhongDuocChon' => $datPhongDuocChon,
            'tongTienPhong' => $tongTienPhong,
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'dat_phong_id' => ['required', 'integer', 'exists:dat_phong,id'],
            'tong_tien_dich_vu' => ['nullable', 'numeric', 'min:0'],
            'giam_gia' => ['nullable', 'numeric', 'min:0'],
            'thue' => ['nullable', 'numeric', 'min:0'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);

        $hoaDon = DB::transaction(function () use ($duLieu) {
            $datPhong = DatPhong::query()
                ->with(['chiTietDatPhong', 'hoaDon'])
                ->findOrFail((int) $duLieu['dat_phong_id']);

            $daCoHoaDon = $datPhong->hoaDon
                ->where('trang_thai', '!=', 'da_huy')
                ->isNotEmpty();

            if ($daCoHoaDon) {
                throw ValidationException::withMessages([
                    'dat_phong_id' => 'Don dat phong nay da co hoa don.',
                ]);
            }

            $tongTienPhong = $this->tinhTongTienPhongTuDatPhong($datPhong);
            $tongTienDichVu = (float) ($duLieu['tong_tien_dich_vu'] ?? 0);
            $giamGia = (float) ($duLieu['giam_gia'] ?? 0);
            $thue = (float) ($duLieu['thue'] ?? 0);

            $tongTien = max(0, $tongTienPhong + $tongTienDichVu - $giamGia + $thue);
            $trangThai = $tongTien > 0 ? 'chua_thanh_toan' : 'da_thanh_toan';

            return HoaDon::query()->create([
                'ma_hoa_don' => $this->taoMaHoaDon(),
                'dat_phong_id' => $datPhong->id,
                'tong_tien_phong' => $tongTienPhong,
                'tong_tien_dich_vu' => $tongTienDichVu,
                'giam_gia' => $giamGia,
                'thue' => $thue,
                'tong_tien' => $tongTien,
                'trang_thai' => $trangThai,
                'thoi_diem_xuat' => now(),
                'nguoi_tao_id' => auth()->id(),
                'ghi_chu' => $duLieu['ghi_chu'] ?? null,
            ]);
        });

        return redirect()
            ->route('hoa-don.show', $hoaDon)
            ->with('success', 'Tao hoa don thanh cong.');
    }

    public function show(HoaDon $hoaDon)
    {
        $hoaDon->load([
            'datPhong.khachHang',
            'datPhong.chiTietDatPhong.phong',
            'nguoiTao',
            'thanhToan.nguoiTao',
        ]);

        $this->dongBoTrangThaiTheoThanhToan($hoaDon);

        $soTienDaThanhToan = $this->tinhTongTienThanhCong($hoaDon);
        $soTienConLai = max(0, (float) $hoaDon->tong_tien - $soTienDaThanhToan);

        return view('hoa_don.show', [
            'hoaDon' => $hoaDon,
            'soTienDaThanhToan' => $soTienDaThanhToan,
            'soTienConLai' => $soTienConLai,
        ]);
    }

    public function capNhatTrangThai(Request $request, HoaDon $hoaDon)
    {
        $duLieu = $request->validate([
            'trang_thai' => ['required', Rule::in(self::TRANG_THAI)],
        ]);

        $trangThaiMoi = $duLieu['trang_thai'];
        $soTienDaThanhToan = $this->tinhTongTienThanhCong($hoaDon->loadMissing('thanhToan'));

        if ($trangThaiMoi === 'da_thanh_toan' && $soTienDaThanhToan < (float) $hoaDon->tong_tien) {
            return redirect()
                ->back()
                ->with('error', 'Khong the chuyen sang da thanh toan khi so tien thu chua du.');
        }

        $hoaDon->update([
            'trang_thai' => $trangThaiMoi,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Cap nhat trang thai hoa don thanh cong.');
    }

    private function tinhTongTienPhongTuDatPhong(DatPhong $datPhong): float
    {
        return (float) $datPhong->chiTietDatPhong->sum(function ($chiTiet) {
            return (float) $chiTiet->gia_phong * (int) $chiTiet->so_dem;
        });
    }

    private function tinhTongTienThanhCong(HoaDon $hoaDon): float
    {
        return (float) $hoaDon->thanhToan
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');
    }

    private function dongBoTrangThaiTheoThanhToan(HoaDon $hoaDon): void
    {
        if ($hoaDon->trang_thai === 'da_huy') {
            return;
        }

        $soTienDaThanhToan = $this->tinhTongTienThanhCong($hoaDon);
        $tongTien = (float) $hoaDon->tong_tien;

        $trangThaiMoi = 'chua_thanh_toan';

        if ($soTienDaThanhToan >= $tongTien) {
            $trangThaiMoi = 'da_thanh_toan';
        } elseif ($soTienDaThanhToan > 0) {
            $trangThaiMoi = 'thanh_toan_mot_phan';
        }

        if ($hoaDon->trang_thai !== $trangThaiMoi) {
            $hoaDon->update([
                'trang_thai' => $trangThaiMoi,
            ]);
        }
    }

    private function taoMaHoaDon(): string
    {
        do {
            $maHoaDon = 'HD' . now()->format('ymdHis') . random_int(10, 99);
        } while (HoaDon::query()->where('ma_hoa_don', $maHoaDon)->exists());

        return $maHoaDon;
    }
}
