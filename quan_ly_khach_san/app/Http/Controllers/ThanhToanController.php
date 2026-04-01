<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use App\Models\ThanhToan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ThanhToanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(['thanh_cong', 'cho_xu_ly', 'that_bai'])],
            'phuong_thuc' => ['nullable', Rule::in(['tien_mat', 'chuyen_khoan', 'the', 'vi_dien_tu', 'khac'])],
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $phuongThuc = $request->input('phuong_thuc');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $danhSachThanhToan = ThanhToan::query()
            ->with(['hoaDon.datPhong.khachHang', 'nguoiTao'])
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_thanh_toan', 'like', "%{$tuKhoa}%")
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
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            })
            ->when($phuongThuc, function ($query) use ($phuongThuc) {
                $query->where('phuong_thuc_thanh_toan', $phuongThuc);
            })
            ->when($tuNgay, function ($query) use ($tuNgay) {
                $query->whereDate('thoi_diem_thanh_toan', '>=', $tuNgay);
            })
            ->when($denNgay, function ($query) use ($denNgay) {
                $query->whereDate('thoi_diem_thanh_toan', '<=', $denNgay);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $danhSachHoaDon = HoaDon::query()
            ->whereIn('trang_thai', ['chua_thanh_toan', 'thanh_toan_mot_phan'])
            ->orderByDesc('id')
            ->take(50)
            ->get();

        return view('thanh_toan.index', compact(
            'danhSachThanhToan',
            'danhSachHoaDon',
            'tuKhoa',
            'trangThai',
            'phuongThuc',
            'tuNgay',
            'denNgay'
        ));
    }

    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'hoa_don_id' => ['required', 'integer', 'exists:hoa_don,id'],
            'so_tien' => ['required', 'numeric', 'min:1000'],
            'phuong_thuc_thanh_toan' => ['required', Rule::in(['tien_mat', 'chuyen_khoan', 'the', 'vi_dien_tu', 'khac'])],
            'thoi_diem_thanh_toan' => ['nullable', 'date'],
            'trang_thai' => ['required', Rule::in(['thanh_cong', 'cho_xu_ly', 'that_bai'])],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
            'redirect_to' => ['nullable', 'string', 'max:20'],
        ]);

        $thanhToan = DB::transaction(function () use ($duLieu) {
            $hoaDon = HoaDon::query()
                ->with('thanhToan')
                ->findOrFail((int) $duLieu['hoa_don_id']);

            if ($hoaDon->trang_thai === 'da_huy') {
                throw ValidationException::withMessages([
                    'hoa_don_id' => 'Khong the thanh toan hoa don da huy.',
                ]);
            }

            if ($duLieu['trang_thai'] === 'thanh_cong') {
                $soTienDaThu = (float) $hoaDon->thanhToan
                    ->where('trang_thai', 'thanh_cong')
                    ->sum('so_tien');

                $soTienConLai = max(0, (float) $hoaDon->tong_tien - $soTienDaThu);

                if ($soTienConLai <= 0) {
                    throw ValidationException::withMessages([
                        'hoa_don_id' => 'Hoa don nay da duoc thanh toan du.',
                    ]);
                }

                if ((float) $duLieu['so_tien'] > $soTienConLai) {
                    throw ValidationException::withMessages([
                        'so_tien' => 'So tien thu vuot qua so tien con lai (' . number_format($soTienConLai, 0, ',', '.') . ' VND).',
                    ]);
                }
            }

            $thanhToan = ThanhToan::query()->create([
                'ma_thanh_toan' => $this->taoMaThanhToan(),
                'hoa_don_id' => $hoaDon->id,
                'so_tien' => (float) $duLieu['so_tien'],
                'phuong_thuc_thanh_toan' => $duLieu['phuong_thuc_thanh_toan'],
                'thoi_diem_thanh_toan' => $duLieu['thoi_diem_thanh_toan'] ?? now(),
                'trang_thai' => $duLieu['trang_thai'],
                'nguoi_tao_id' => auth()->id(),
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
                ->with('success', 'Ghi nhan thanh toan thanh cong.');
        }

        return redirect()
            ->route('thanh-toan.index')
            ->with('success', 'Ghi nhan thanh toan thanh cong.');
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
    }

    private function taoMaThanhToan(): string
    {
        do {
            $maThanhToan = 'TT' . now()->format('ymdHis') . random_int(10, 99);
        } while (ThanhToan::query()->where('ma_thanh_toan', $maThanhToan)->exists());

        return $maThanhToan;
    }
}
