<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\HoaDon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $truyVanHoaDon = $this->taoTruyVanHoaDon($tuKhoa, $trangThai, $tuNgay, $denNgay);

        $danhSachHoaDon = (clone $truyVanHoaDon)
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $danhSachHoaDon->setCollection(
            $danhSachHoaDon->getCollection()->map(function (HoaDon $hoaDon) {
                $this->dongBoTrangThaiTheoThanhToan($hoaDon);

                return $this->boSungDuLieuHoaDon($hoaDon);
            })
        );

        $danhSachHoaDonDaLoc = (clone $truyVanHoaDon)
            ->get()
<<<<<<< HEAD
            ->map(function (HoaDon $hoaDon) {
                $this->dongBoTrangThaiTheoThanhToan($hoaDon);

                return $this->boSungDuLieuHoaDon($hoaDon);
            });
=======
            ->map(fn(HoaDon $hoaDon) => $this->boSungDuLieuHoaDon($hoaDon));
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e

        $thongKe = $this->tongHopHoaDon($danhSachHoaDonDaLoc);

        $hoaDonCanChuY = $danhSachHoaDonDaLoc
            ->filter(fn(HoaDon $hoaDon) => $hoaDon->trang_thai_hien_thi !== 'da_huy' && $hoaDon->so_tien_con_lai > 0)
            ->sortByDesc(fn(HoaDon $hoaDon) => ($hoaDon->can_thu_gap ? 1000000000000 : 0) + $hoaDon->so_tien_con_lai)
            ->take(5)
            ->values();

        return view('hoa_don.index', compact(
            'danhSachHoaDon',
            'tuKhoa',
            'trangThai',
            'tuNgay',
            'denNgay',
            'thongKe',
            'hoaDonCanChuY'
        ));
    }

    public function create(Request $request)
    {
        $danhSachDatPhong = DatPhong::query()
            ->with(['khachHang', 'chiTietDatPhong.phong', 'suDungDichVu.dichVu'])
            ->whereIn('trang_thai', ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong'])
            ->whereDoesntHave('hoaDon', function ($query) {
                $query->where('trang_thai', '!=', 'da_huy');
            })
            ->latest('id')
            ->get();

        $datPhongDuocChon = null;
        $tongTienPhong = 0;
        $tongTienDichVu = 0;

        if ($request->filled('dat_phong_id')) {
            $datPhongDuocChon = $danhSachDatPhong->firstWhere('id', (int) $request->input('dat_phong_id'));
            $tongTienPhong = $datPhongDuocChon
                ? $this->tinhTongTienPhongTuDatPhong($datPhongDuocChon)
                : 0;
            $tongTienDichVu = $datPhongDuocChon
                ? $this->tinhTongTienDichVuTuDatPhong($datPhongDuocChon)
                : 0;
        }

        return view('hoa_don.create', [
            'danhSachDatPhong' => $danhSachDatPhong,
            'datPhongDuocChon' => $datPhongDuocChon,
            'tongTienPhong' => $tongTienPhong,
            'tongTienDichVu' => $tongTienDichVu,
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'dat_phong_id' => ['required', 'integer', 'exists:dat_phong,id'],
            'giam_gia' => ['nullable', 'numeric', 'min:0'],
            'thue' => ['nullable', 'numeric', 'min:0'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);

        $hoaDon = DB::transaction(function () use ($duLieu) {
            $datPhong = DatPhong::query()
                ->with(['chiTietDatPhong', 'hoaDon'])
                ->findOrFail((int) $duLieu['dat_phong_id']);

            if (!in_array($datPhong->trang_thai, ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong'], true)) {
                throw ValidationException::withMessages([
                    'dat_phong_id' => 'Chi duoc tao hoa don cho don dat phong da xac nhan, da nhan phong hoac da tra phong.',
                ]);
            }

            $daCoHoaDon = $datPhong->hoaDon
                ->where('trang_thai', '!=', 'da_huy')
                ->isNotEmpty();

            if ($daCoHoaDon) {
                throw ValidationException::withMessages([
                    'dat_phong_id' => 'Đơn đặt phòng này đã có hóa đơn.',
                ]);
            }

            $tongTienPhong = $this->tinhTongTienPhongTuDatPhong($datPhong);
            $tongTienDichVu = $this->tinhTongTienDichVuTuDatPhong($datPhong);
            $giamGia = (float) ($duLieu['giam_gia'] ?? 0);
            $thue = (float) ($duLieu['thue'] ?? 0);

            $tongTien = max(0, $tongTienPhong + $tongTienDichVu - $giamGia + $thue);
            $trangThai = $tongTien > 0 ? 'chua_thanh_toan' : 'da_thanh_toan';

            $hoaDon = HoaDon::query()->create([
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

            $hoaDon->giaiPhongSauKhiHoanTatThanhToan();

            return $hoaDon;
        });

        return redirect()
            ->route('hoa-don.show', $hoaDon)
            ->with('success', 'Tạo hóa đơn thành công.');
    }

    public function show(HoaDon $hoaDon)
    {
        $hoaDon->load([
            'datPhong.khachHang',
            'datPhong.chiTietDatPhong.phong',
            'datPhong.suDungDichVu.dichVu',
            'datPhong.suDungDichVu.nguoiTao',
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
        $tongTien = (float) $hoaDon->tong_tien;

        if ($trangThaiMoi === 'da_thanh_toan' && $soTienDaThanhToan < $tongTien) {
            return redirect()
                ->back()
                ->with('error', 'Không thể chuyển sang đã thanh toán khi số tiền thu chưa đủ.');
<<<<<<< HEAD
        }

        if ($trangThaiMoi === 'chua_thanh_toan' && $soTienDaThanhToan > 0) {
            return redirect()
                ->back()
                ->with('error', 'Khong the dat hoa don ve chua thanh toan khi da co giao dich thanh cong.');
        }

        if (
            $trangThaiMoi === 'thanh_toan_mot_phan'
            && ($soTienDaThanhToan <= 0 || $soTienDaThanhToan >= $tongTien)
        ) {
            return redirect()
                ->back()
                ->with('error', 'Trang thai thanh toan mot phan chi hop le khi da thu mot phan va chua thu du.');
        }

        if ($trangThaiMoi === 'da_huy' && $soTienDaThanhToan > 0) {
            return redirect()
                ->back()
                ->with('error', 'Khong the huy hoa don da ghi nhan thanh toan thanh cong.');
=======
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        $hoaDon->update([
            'trang_thai' => $trangThaiMoi,
        ]);

        $hoaDon->refresh()->giaiPhongSauKhiHoanTatThanhToan();

        return redirect()
            ->back()
            ->with('success', 'Cập nhật trạng thái hóa đơn thành công.');
    }

    private function taoTruyVanHoaDon(?string $tuKhoa, ?string $trangThai, ?string $tuNgay, ?string $denNgay)
    {
        return HoaDon::query()
<<<<<<< HEAD
            ->with([
                'datPhong.khachHang',
                'datPhong.chiTietDatPhong.phong',
                'datPhong.suDungDichVu',
                'thanhToan',
            ])
=======
            ->with(['datPhong.khachHang', 'datPhong.chiTietDatPhong.phong', 'thanhToan'])
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
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
            });
    }

    private function boSungDuLieuHoaDon(HoaDon $hoaDon): HoaDon
    {
        $soTienDaThu = $this->tinhTongTienThanhCong($hoaDon);
        $tongTien = (float) $hoaDon->tong_tien;
        $soTienConLai = max(0, $tongTien - $soTienDaThu);
        $phanTramThanhToan = $tongTien > 0 ? round(min(100, ($soTienDaThu / $tongTien) * 100), 1) : 100;
        $trangThaiHienThi = $this->xacDinhTrangThaiHienThi($hoaDon, $soTienDaThu);

        $ngayDenHanThu = $hoaDon->datPhong?->ngay_tra_phong_thuc_te
            ?? $hoaDon->datPhong?->ngay_tra_phong_du_kien
            ?? $hoaDon->thoi_diem_xuat;

        $ngayDenHanThu = $ngayDenHanThu ? Carbon::parse($ngayDenHanThu) : null;

        $canThuGap = $trangThaiHienThi !== 'da_huy'
            && $soTienConLai > 0
            && $ngayDenHanThu
            && $ngayDenHanThu->copy()->startOfDay()->lt(now()->startOfDay());

        $hoaDon->setAttribute('so_tien_da_thu', $soTienDaThu);
        $hoaDon->setAttribute('so_tien_con_lai', $soTienConLai);
        $hoaDon->setAttribute('phan_tram_thanh_toan', $phanTramThanhToan);
        $hoaDon->setAttribute('trang_thai_hien_thi', $trangThaiHienThi);
        $hoaDon->setAttribute('ngay_den_han_thu', $ngayDenHanThu);
        $hoaDon->setAttribute('can_thu_gap', $canThuGap);
        $hoaDon->setAttribute('tong_so_phong', (int) ($hoaDon->datPhong?->chiTietDatPhong?->count() ?? 0));
        $hoaDon->setAttribute('tong_so_dem', (int) ($hoaDon->datPhong?->chiTietDatPhong?->sum('so_dem') ?? 0));

        return $hoaDon;
    }

    private function tongHopHoaDon(Collection $danhSachHoaDon): array
    {
        $tongHoaDon = $danhSachHoaDon->count();
        $tongGiaTri = (float) $danhSachHoaDon->sum('tong_tien');
        $tongDaThu = (float) $danhSachHoaDon->sum(function (HoaDon $hoaDon) {
            return min((float) $hoaDon->tong_tien, (float) $hoaDon->so_tien_da_thu);
        });
        $tongConLai = (float) $danhSachHoaDon->sum('so_tien_con_lai');

        return [
            'tong' => $tongHoaDon,
            'tong_gia_tri' => $tongGiaTri,
            'da_thu' => $tongDaThu,
            'con_lai' => $tongConLai,
            'ty_le_thu_hoi' => $tongGiaTri > 0 ? round(($tongDaThu / $tongGiaTri) * 100, 1) : 0,
            'gia_tri_trung_binh' => $tongHoaDon > 0 ? round($tongGiaTri / $tongHoaDon, 0) : 0,
            'chua_thanh_toan' => $danhSachHoaDon->where('trang_thai_hien_thi', 'chua_thanh_toan')->count(),
            'thanh_toan_mot_phan' => $danhSachHoaDon->where('trang_thai_hien_thi', 'thanh_toan_mot_phan')->count(),
            'da_thanh_toan' => $danhSachHoaDon->where('trang_thai_hien_thi', 'da_thanh_toan')->count(),
            'da_huy' => $danhSachHoaDon->where('trang_thai_hien_thi', 'da_huy')->count(),
            'can_thu_gap' => $danhSachHoaDon->where('can_thu_gap', true)->count(),
        ];
    }

    private function xacDinhTrangThaiHienThi(HoaDon $hoaDon, float $soTienDaThanhToan): string
    {
        if ($hoaDon->trang_thai === 'da_huy') {
            return 'da_huy';
        }

        if ($soTienDaThanhToan >= (float) $hoaDon->tong_tien) {
            return 'da_thanh_toan';
        }

        if ($soTienDaThanhToan > 0) {
            return 'thanh_toan_mot_phan';
        }

        return 'chua_thanh_toan';
    }

    private function tinhTongTienPhongTuDatPhong(DatPhong $datPhong): float
    {
        return $datPhong->tinhTongTienPhong();
    }

    private function tinhTongTienDichVuTuDatPhong(DatPhong $datPhong): float
    {
        return $datPhong->tinhTongTienDichVu();
    }

    private function tinhTongTienThanhCong(HoaDon $hoaDon): float
    {
        return (float) $hoaDon->thanhToan
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');
    }

    private function dongBoTrangThaiTheoThanhToan(HoaDon $hoaDon): void
    {
        $hoaDon->dongBoGiaTriTuDatPhong(false);
    }

    private function taoMaHoaDon(): string
    {
        do {
            $maHoaDon = 'HD' . now()->format('ymdHis') . random_int(10, 99);
        } while (HoaDon::query()->where('ma_hoa_don', $maHoaDon)->exists());

        return $maHoaDon;
    }
}
