<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\ThanhToan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ThanhToanController extends Controller
{
    private const TRANG_THAI_GIAO_DICH = [
        'thanh_cong',
        'cho_xu_ly',
        'that_bai',
    ];

    private const PHUONG_THUC_THANH_TOAN = [
        'tien_mat',
        'chuyen_khoan',
        'the',
        'vi_dien_tu',
        'khac',
    ];

    private const PHUONG_THUC_KHACH_HANG_CO_THE_GUI = [
        'chuyen_khoan',
        'the',
        'vi_dien_tu',
    ];

    private const NGUON_TAO = [
        ThanhToan::NGUON_TAO_NOI_BO,
        ThanhToan::NGUON_TAO_KHACH_HANG,
    ];

    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(self::TRANG_THAI_GIAO_DICH)],
            'phuong_thuc' => ['nullable', Rule::in(self::PHUONG_THUC_THANH_TOAN)],
            'nguon_tao' => ['nullable', Rule::in(self::NGUON_TAO)],
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $phuongThuc = $request->input('phuong_thuc');
        $nguonTao = $request->input('nguon_tao');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $truyVanThanhToan = ThanhToan::query()
            ->with(['hoaDon.datPhong.khachHang', 'nguoiTao', 'nguoiXuLy'])
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_thanh_toan', 'like', "%{$tuKhoa}%")
                        ->orWhere('ma_tham_chieu', 'like', "%{$tuKhoa}%")
                        ->orWhereHas('hoaDon', function ($hoaDonQuery) use ($tuKhoa) {
                            $hoaDonQuery
                                ->where('ma_hoa_don', 'like', "%{$tuKhoa}%")
                                ->orWhereHas('datPhong', function ($datPhongQuery) use ($tuKhoa) {
                                    $datPhongQuery
                                        ->where('ma_dat_phong', 'like', "%{$tuKhoa}%")
                                        ->orWhereHas('khachHang', function ($khachHangQuery) use ($tuKhoa) {
                                            $khachHangQuery
                                                ->where('ho_ten', 'like', "%{$tuKhoa}%")
                                                ->orWhere('so_dien_thoai', 'like', "%{$tuKhoa}%");
                                        });
                                });
                        });
                });
            })
            ->when($trangThai, fn ($query) => $query->where('trang_thai', $trangThai))
            ->when($phuongThuc, fn ($query) => $query->where('phuong_thuc_thanh_toan', $phuongThuc))
            ->when($nguonTao, fn ($query) => $query->where('nguon_tao', $nguonTao))
            ->when($tuNgay, fn ($query) => $query->whereDate('thoi_diem_thanh_toan', '>=', $tuNgay))
            ->when($denNgay, fn ($query) => $query->whereDate('thoi_diem_thanh_toan', '<=', $denNgay));

        $thongKe = [
            'tong_giao_dich' => (clone $truyVanThanhToan)->count(),
            'thanh_cong' => (clone $truyVanThanhToan)->where('trang_thai', 'thanh_cong')->count(),
            'cho_xu_ly' => (clone $truyVanThanhToan)->where('trang_thai', 'cho_xu_ly')->count(),
            'that_bai' => (clone $truyVanThanhToan)->where('trang_thai', 'that_bai')->count(),
            'tong_tien_thanh_cong' => (float) (clone $truyVanThanhToan)->where('trang_thai', 'thanh_cong')->sum('so_tien'),
            'yeu_cau_khach_hang' => (clone $truyVanThanhToan)->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)->count(),
        ];

        $danhSachYeuCauKhachHangChoXuLy = ThanhToan::query()
            ->with(['hoaDon.datPhong.khachHang', 'nguoiTao'])
            ->where('trang_thai', 'cho_xu_ly')
            ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
            ->latest('id')
            ->take(6)
            ->get();

        $tongTienChoDuyetKhachHang = (float) $danhSachYeuCauKhachHangChoXuLy->sum('so_tien');

        $danhSachThanhToan = (clone $truyVanThanhToan)
            ->orderByRaw("
                CASE
                    WHEN trang_thai = 'cho_xu_ly' AND nguon_tao = ? THEN 0
                    WHEN trang_thai = 'cho_xu_ly' THEN 1
                    ELSE 2
                END
            ", [ThanhToan::NGUON_TAO_KHACH_HANG])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $danhSachHoaDon = HoaDon::query()
            ->with([
                'datPhong.chiTietDatPhong',
                'datPhong.suDungDichVu',
                'thanhToan',
            ])
            ->where('trang_thai', '!=', 'da_huy')
            ->orderByDesc('id')
            ->take(50)
            ->get();
        $danhSachHoaDon = $danhSachHoaDon
            ->map(function (HoaDon $hoaDon) {
                $hoaDon->dongBoGiaTriTuDatPhong(false);

                return $hoaDon;
            })
            ->filter(function (HoaDon $hoaDon) {
                return $hoaDon->trang_thai !== 'da_huy'
                    && (float) $hoaDon->tong_tien > $hoaDon->tinhTongTienDaThu();
            })
            ->values();

        return view('thanh_toan.index', compact(
            'danhSachThanhToan',
            'danhSachHoaDon',
            'tuKhoa',
            'trangThai',
            'phuongThuc',
            'nguonTao',
            'tuNgay',
            'denNgay',
            'thongKe',
            'danhSachYeuCauKhachHangChoXuLy',
            'tongTienChoDuyetKhachHang'
        ));
    }

    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'hoa_don_id' => ['required', 'integer', 'exists:hoa_don,id'],
            'so_tien' => ['required', 'numeric', 'min:1000'],
            'phuong_thuc_thanh_toan' => ['required', Rule::in(self::PHUONG_THUC_THANH_TOAN)],
            'ma_tham_chieu' => ['nullable', 'string', 'max:100'],
            'thoi_diem_thanh_toan' => ['nullable', 'date'],
            'trang_thai' => ['required', Rule::in(self::TRANG_THAI_GIAO_DICH)],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
            'redirect_to' => ['nullable', 'string', 'max:20'],
        ]);

        $thanhToan = DB::transaction(function () use ($duLieu) {
            $hoaDon = HoaDon::query()
                ->with('thanhToan')
                ->findOrFail((int) $duLieu['hoa_don_id']);

            $this->baoDamHoaDonCoTheNhanThanhToan($hoaDon);

            if ($duLieu['trang_thai'] === 'thanh_cong') {
                $this->baoDamSoTienKhongVuotQuaConLai(
                    $hoaDon,
                    (float) $duLieu['so_tien']
                );
            }

            $thanhToan = ThanhToan::query()->create([
                'ma_thanh_toan' => $this->taoMaThanhToan(),
                'hoa_don_id' => $hoaDon->id,
                'so_tien' => (float) $duLieu['so_tien'],
                'phuong_thuc_thanh_toan' => $duLieu['phuong_thuc_thanh_toan'],
                'ma_tham_chieu' => $duLieu['ma_tham_chieu'] ?? null,
                'thoi_diem_thanh_toan' => $duLieu['thoi_diem_thanh_toan'] ?? now(),
                'trang_thai' => $duLieu['trang_thai'],
                'nguon_tao' => ThanhToan::NGUON_TAO_NOI_BO,
                'nguoi_tao_id' => auth()->id(),
                'nguoi_xu_ly_id' => $duLieu['trang_thai'] === 'cho_xu_ly' ? null : auth()->id(),
                'thoi_diem_xu_ly' => $duLieu['trang_thai'] === 'cho_xu_ly' ? null : now(),
                'ghi_chu' => $duLieu['ghi_chu'] ?? null,
            ]);

            if ($duLieu['trang_thai'] === 'thanh_cong') {
                $this->dongBoTrangThaiHoaDon($hoaDon->fresh('thanhToan'));
            }

            return $thanhToan;
        });

        if (($duLieu['redirect_to'] ?? null) === 'hoa_don_show') {
            return redirect()
                ->route('hoa-don.show', $thanhToan->hoa_don_id)
                ->with('success', 'Ghi nhận thanh toán thành công.');
        }

        return redirect()
            ->route('thanh-toan.index')
            ->with('success', 'Ghi nhận thanh toán thành công.');
    }

    public function storeYeuCauKhachHang(Request $request, HoaDon $hoaDon)
    {
        $hoaDon->load('datPhong');
        $this->xacThucHoaDonThuocKhachDangNhap($hoaDon);

        $duLieu = $request->validate([
            'so_tien' => ['required', 'numeric', 'min:1000'],
            'phuong_thuc_thanh_toan' => ['required', Rule::in(self::PHUONG_THUC_KHACH_HANG_CO_THE_GUI)],
            'ma_tham_chieu' => ['nullable', 'string', 'max:100'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($hoaDon, $duLieu) {
            $hoaDon = HoaDon::query()
                ->with('thanhToan')
                ->findOrFail($hoaDon->id);

            $this->baoDamHoaDonCoTheNhanThanhToan($hoaDon);
            $this->baoDamSoTienKhongVuotQuaConLai(
                $hoaDon,
                (float) $duLieu['so_tien'],
                null,
                true
            );

            ThanhToan::query()->create([
                'ma_thanh_toan' => $this->taoMaThanhToan(),
                'hoa_don_id' => $hoaDon->id,
                'so_tien' => (float) $duLieu['so_tien'],
                'phuong_thuc_thanh_toan' => $duLieu['phuong_thuc_thanh_toan'],
                'ma_tham_chieu' => $duLieu['ma_tham_chieu'] ?? null,
                'thoi_diem_thanh_toan' => now(),
                'trang_thai' => 'cho_xu_ly',
                'nguon_tao' => ThanhToan::NGUON_TAO_KHACH_HANG,
                'nguoi_tao_id' => auth()->id(),
                'nguoi_xu_ly_id' => null,
                'thoi_diem_xu_ly' => null,
                'ghi_chu' => $duLieu['ghi_chu'] ?? null,
            ]);
        });

        return redirect()
            ->route('booking.hoa-don.show', $hoaDon)
            ->with('success', 'Đã ghi nhận yêu cầu thanh toán. Giao dịch sẽ được nội bộ đối soát trước khi xác nhận thành công.');
    }

    public function capNhatTrangThai(Request $request, ThanhToan $thanhToan)
    {
        $duLieu = $request->validate([
            'trang_thai' => ['required', Rule::in(['thanh_cong', 'that_bai'])],
        ]);

        if ($thanhToan->trang_thai !== 'cho_xu_ly') {
            return redirect()
                ->back()
                ->with('error', 'Chỉ có thể xử lý các giao dịch đang chờ xử lý.');
        }

        DB::transaction(function () use ($thanhToan, $duLieu) {
            $hoaDon = $thanhToan->hoaDon()->with('thanhToan')->firstOrFail();

            if ($duLieu['trang_thai'] === 'thanh_cong') {
                $this->baoDamHoaDonCoTheNhanThanhToan($hoaDon);
                $this->baoDamSoTienKhongVuotQuaConLai(
                    $hoaDon,
                    (float) $thanhToan->so_tien,
                    $thanhToan
                );
            }

            $thanhToan->update([
                'trang_thai' => $duLieu['trang_thai'],
                'nguoi_xu_ly_id' => auth()->id(),
                'thoi_diem_xu_ly' => now(),
            ]);

            if ($duLieu['trang_thai'] === 'thanh_cong') {
                $this->dongBoTrangThaiHoaDon($hoaDon->fresh('thanhToan'));
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Đã cập nhật trạng thái giao dịch thành công.');
    }

    private function dongBoTrangThaiHoaDon(HoaDon $hoaDon): void
    {
        if ($hoaDon->trang_thai === 'da_huy') {
            return;
        }

        $soTienDaThu = (float) $hoaDon->thanhToan
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');

        $tongTien = (float) $hoaDon->tong_tien;
        $trangThaiMoi = 'chua_thanh_toan';

        if ($soTienDaThu >= $tongTien) {
            $trangThaiMoi = 'da_thanh_toan';
        } elseif ($soTienDaThu > 0) {
            $trangThaiMoi = 'thanh_toan_mot_phan';
        }

        $hoaDon->update([
            'trang_thai' => $trangThaiMoi,
        ]);

        $hoaDon->refresh()->giaiPhongSauKhiHoanTatThanhToan();
    }

    private function baoDamHoaDonCoTheNhanThanhToan(HoaDon $hoaDon): void
    {
        if ($hoaDon->trang_thai === 'da_huy') {
            throw ValidationException::withMessages([
                'hoa_don_id' => 'Không thể thanh toán hóa đơn đã hủy.',
            ]);
        }

        if ((float) $hoaDon->tong_tien <= 0) {
            throw ValidationException::withMessages([
                'hoa_don_id' => 'Hóa đơn này không phát sinh số tiền cần thu.',
            ]);
        }
    }

    private function baoDamSoTienKhongVuotQuaConLai(
        HoaDon $hoaDon,
        float $soTien,
        ?ThanhToan $boQuaThanhToan = null,
        bool $baoGomGiaoDichChoXuLy = false
    ): void {
        $soTienDaThu = $this->tinhTongTienTheoTrangThai($hoaDon, 'thanh_cong', $boQuaThanhToan);
        $tongTienConLai = max(0, (float) $hoaDon->tong_tien - $soTienDaThu);

        if ($baoGomGiaoDichChoXuLy) {
            $tongTienConLai = max(
                0,
                $tongTienConLai - $this->tinhTongTienTheoTrangThai($hoaDon, 'cho_xu_ly', $boQuaThanhToan)
            );
        }

        if ($tongTienConLai <= 0) {
            throw ValidationException::withMessages([
                'hoa_don_id' => 'Hóa đơn này đã đủ số tiền cần xử lý.',
            ]);
        }

        if ($soTien > $tongTienConLai) {
            throw ValidationException::withMessages([
                'so_tien' => 'Số tiền vượt quá phần còn lại có thể xử lý (' . number_format($tongTienConLai, 0, ',', '.') . ' VNĐ).',
            ]);
        }
    }

    private function tinhTongTienTheoTrangThai(HoaDon $hoaDon, string $trangThai, ?ThanhToan $boQuaThanhToan = null): float
    {
        if ($hoaDon->relationLoaded('thanhToan')) {
            return (float) $hoaDon->thanhToan
                ->filter(function (ThanhToan $thanhToan) use ($trangThai, $boQuaThanhToan) {
                    return $thanhToan->trang_thai === $trangThai
                        && (! $boQuaThanhToan || $thanhToan->id !== $boQuaThanhToan->id);
                })
                ->sum('so_tien');
        }

        return (float) $hoaDon->thanhToan()
            ->when($boQuaThanhToan, fn ($query) => $query->where('id', '!=', $boQuaThanhToan->id))
            ->where('trang_thai', $trangThai)
            ->sum('so_tien');
    }

    private function xacThucHoaDonThuocKhachDangNhap(HoaDon $hoaDon): void
    {
        $nguoiDung = auth()->user();
        $khachHangDangNhap = KhachHang::timTheoTaiKhoan($nguoiDung);

        abort_unless(
            $khachHangDangNhap
                && $hoaDon->datPhong
                && $hoaDon->datPhong->khach_hang_id === $khachHangDangNhap->id,
            404
        );
    }

    private function taoMaThanhToan(): string
    {
        do {
            $maThanhToan = 'TT' . now()->format('ymdHis') . random_int(10, 99);
        } while (ThanhToan::query()->where('ma_thanh_toan', $maThanhToan)->exists());

        return $maThanhToan;
    }
}
