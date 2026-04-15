<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatPhongController extends Controller
{
    private const TRANG_THAI_DAT_PHONG = [
        'cho_xac_nhan',
        'da_xac_nhan',
        'da_nhan_phong',
        'da_tra_phong',
        'da_huy',
    ];

    private const TRANG_THAI_XUNG_DOT = [
        'cho_xac_nhan',
        'da_xac_nhan',
        'da_nhan_phong',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $nguonDat = $request->input('nguon_dat');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $danhSachDatPhong = DatPhong::query()
            ->with(['khachHang', 'chiTietDatPhong.phong'])
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery->where('ma_dat_phong', 'like', "%{$tuKhoa}%")
                        ->orWhereHas('khachHang', function ($khachHangQuery) use ($tuKhoa) {
                            $khachHangQuery
                                ->where('ho_ten', 'like', "%{$tuKhoa}%")
                                ->orWhere('so_dien_thoai', 'like', "%{$tuKhoa}%")
                                ->orWhere('email', 'like', "%{$tuKhoa}%");
                        });
                });
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            })
            ->when($nguonDat, function ($query) use ($nguonDat) {
                $query->where('nguon_dat', $nguonDat);
            })
            ->when($tuNgay, function ($query) use ($tuNgay) {
                $query->whereDate('ngay_nhan_phong_du_kien', '>=', $tuNgay);
            })
            ->when($denNgay, function ($query) use ($denNgay) {
                $query->whereDate('ngay_tra_phong_du_kien', '<=', $denNgay);
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('dat_phong.index', compact(
            'danhSachDatPhong',
            'tuKhoa',
            'trangThai',
            'nguonDat',
            'tuNgay',
            'denNgay'
        ));
    }

    public function create()
    {
        $danhSachPhong = Phong::query()
            ->with('loaiPhong')
            ->where('tinh_trang_hoat_dong', 'hoat_dong')
            ->orderBy('so_phong')
            ->get();

        return view('dat_phong.create', [
            'danhSachPhong' => $danhSachPhong,
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'phong_id' => ['required', 'integer', 'exists:phong,id'],
            'ho_ten' => ['required', 'string', 'max:100'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:100', 'required_without:so_dien_thoai'],
            'ngay_nhan' => ['required', 'date', 'after_or_equal:today'],
            'ngay_tra' => ['required', 'date', 'after:ngay_nhan'],
            'so_nguoi_lon' => ['required', 'integer', 'min:1', 'max:20'],
            'so_tre_em' => ['nullable', 'integer', 'min:0', 'max:20'],
            'trang_thai' => ['required', Rule::in(['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'])],
            'nguon_dat' => ['required', Rule::in(['truc_tiep', 'website', 'dien_thoai', 'zalo', 'khac'])],
            'yeu_cau_dac_biet' => ['nullable', 'string', 'max:1000'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);

        $phong = Phong::query()
            ->with('loaiPhong')
            ->findOrFail((int) $duLieu['phong_id']);

        if ($phong->tinh_trang_hoat_dong !== 'hoat_dong' || !$phong->loaiPhong || $phong->loaiPhong->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages([
                'phong_id' => 'Phòng này hiện không sẵn sàng để đặt.',
            ]);
        }

        $soNguoiToiDa = (int) ($phong->loaiPhong->so_nguoi_toi_da ?? 1);
        $tongKhach = (int) $duLieu['so_nguoi_lon'] + (int) ($duLieu['so_tre_em'] ?? 0);

        if ($tongKhach > $soNguoiToiDa) {
            throw ValidationException::withMessages([
                'so_nguoi_lon' => 'Tổng số khách vượt quá sức chứa tối đa của phòng (' . $soNguoiToiDa . ').',
            ]);
        }

        $giaMotDem = (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong->gia_mot_dem ?? 0);
        $ngayNhan = Carbon::parse($duLieu['ngay_nhan'])->startOfDay();
        $ngayTra = Carbon::parse($duLieu['ngay_tra'])->startOfDay();
        $soDem = max(1, $ngayTra->diffInDays($ngayNhan));

        $datPhong = DB::transaction(function () use ($duLieu, $phong, $giaMotDem, $ngayNhan, $ngayTra, $soDem) {
            if (!$this->phongConTrong($phong->id, $ngayNhan->toDateString(), $ngayTra->toDateString())) {
                throw ValidationException::withMessages([
                    'phong_id' => 'Phòng đã được đặt trong khoảng thời gian này. Vui lòng chọn phòng khác.',
                ]);
            }

            $khachHang = $this->timHoacTaoKhachHang($duLieu);
            $thoiDiemNhanPhong = $duLieu['trang_thai'] === 'da_nhan_phong' ? now() : null;

            $datPhong = DatPhong::query()->create([
                'ma_dat_phong' => $this->taoMaDatPhong(),
                'khach_hang_id' => $khachHang->id,
                'nguoi_tao_id' => auth()->id(),
                'ngay_dat' => now(),
                'ngay_nhan_phong_du_kien' => $ngayNhan->toDateString(),
                'ngay_tra_phong_du_kien' => $ngayTra->toDateString(),
                'ngay_nhan_phong_thuc_te' => $thoiDiemNhanPhong,
                'so_nguoi_lon' => (int) $duLieu['so_nguoi_lon'],
                'so_tre_em' => (int) ($duLieu['so_tre_em'] ?? 0),
                'trang_thai' => $duLieu['trang_thai'],
                'nguon_dat' => $duLieu['nguon_dat'],
                'yeu_cau_dac_biet' => $duLieu['yeu_cau_dac_biet'] ?? null,
                'ghi_chu' => $duLieu['ghi_chu'] ?? null,
            ]);

            $datPhong->chiTietDatPhong()->create([
                'phong_id' => $phong->id,
                'gia_phong' => $giaMotDem,
                'so_dem' => $soDem,
                'so_nguoi_lon' => (int) $duLieu['so_nguoi_lon'],
                'so_tre_em' => (int) ($duLieu['so_tre_em'] ?? 0),
                'ngay_nhan_phong_thuc_te' => $thoiDiemNhanPhong,
                'trang_thai' => $this->mapTrangThaiChiTiet($duLieu['trang_thai']) ?? 'da_dat',
            ]);

            $this->dongBoTrangThaiPhongTheoDatPhong($datPhong->load('chiTietDatPhong.phong'));

            return $datPhong;
        });

        return redirect()
            ->route('dat-phong.show', $datPhong)
            ->with('success', 'Tạo đơn đặt phòng thành công.');
    }

    public function show(DatPhong $datPhong)
    {
        $datPhong->load([
            'khachHang',
            'nguoiTao',
            'chiTietDatPhong.phong.loaiPhong',
            'hoaDon',
        ]);

        $tongTienPhong = $datPhong->chiTietDatPhong->sum(function (ChiTietDatPhong $chiTiet) {
            return (float) $chiTiet->gia_phong * (int) $chiTiet->so_dem;
        });

        $hoaDonHienTai = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();

        return view('dat_phong.show', [
            'datPhong' => $datPhong,
            'tongTienPhong' => $tongTienPhong,
            'hoaDonHienTai' => $hoaDonHienTai,
        ]);
    }

    public function capNhatTrangThai(Request $request, DatPhong $datPhong)
    {
        $duLieu = $request->validate([
            'trang_thai' => [
                'required',
                Rule::in(self::TRANG_THAI_DAT_PHONG),
            ],
        ]);

        $daTaoHoaDonTuDong = false;

        DB::transaction(function () use ($datPhong, $duLieu, &$daTaoHoaDonTuDong) {
            $trangThaiMoi = $duLieu['trang_thai'];
            $duLieuCapNhatDatPhong = [
                'trang_thai' => $trangThaiMoi,
            ];

            if ($trangThaiMoi === 'da_nhan_phong' && !$datPhong->ngay_nhan_phong_thuc_te) {
                $duLieuCapNhatDatPhong['ngay_nhan_phong_thuc_te'] = now();
            }

            if ($trangThaiMoi === 'da_tra_phong' && !$datPhong->ngay_tra_phong_thuc_te) {
                $duLieuCapNhatDatPhong['ngay_tra_phong_thuc_te'] = now();
            }

            $datPhong->update($duLieuCapNhatDatPhong);

            $trangThaiChiTiet = $this->mapTrangThaiChiTiet($trangThaiMoi);

            if ($trangThaiChiTiet) {
                $duLieuCapNhatChiTiet = [
                    'trang_thai' => $trangThaiChiTiet,
                ];

                if ($trangThaiMoi === 'da_nhan_phong') {
                    $duLieuCapNhatChiTiet['ngay_nhan_phong_thuc_te'] = now();
                }

                if ($trangThaiMoi === 'da_tra_phong') {
                    $duLieuCapNhatChiTiet['ngay_tra_phong_thuc_te'] = now();
                }

                $datPhong->chiTietDatPhong()->update($duLieuCapNhatChiTiet);
            }

            $datPhongSauCapNhat = $datPhong->fresh(['chiTietDatPhong.phong', 'chiTietDatPhong', 'hoaDon']);

            $this->dongBoTrangThaiPhongTheoDatPhong($datPhongSauCapNhat);

            if ($trangThaiMoi === 'da_tra_phong') {
                $daTaoHoaDonTuDong = $this->taoHoaDonTuDongNeuCan($datPhongSauCapNhat);
            }
        });

        $thongBao = $daTaoHoaDonTuDong
            ? 'Cập nhật trạng thái đơn đặt phòng thành công. Hệ thống đã tự động tạo hóa đơn.'
            : 'Cập nhật trạng thái đơn đặt phòng thành công.';

        return redirect()
            ->back()
            ->with('success', $thongBao);
    }

    private function mapTrangThaiChiTiet(string $trangThaiDatPhong): ?string
    {
        return match ($trangThaiDatPhong) {
            'cho_xac_nhan', 'da_xac_nhan' => 'da_dat',
            'da_nhan_phong' => 'dang_o',
            'da_tra_phong' => 'da_tra_phong',
            'da_huy' => 'da_huy',
            default => null,
        };
    }

    private function mapTrangThaiPhong(string $trangThaiDatPhong): string
    {
        return match ($trangThaiDatPhong) {
            'cho_xac_nhan', 'da_xac_nhan' => 'da_dat',
            'da_nhan_phong' => 'dang_su_dung',
            'da_tra_phong', 'da_huy' => 'trong',
            default => 'trong',
        };
    }

    private function phongConTrong(int $phongId, string $ngayNhan, string $ngayTra): bool
    {
        $coXungDot = ChiTietDatPhong::query()
            ->where('phong_id', $phongId)
            ->whereHas('datPhong', function ($datPhongQuery) use ($ngayNhan, $ngayTra) {
                $datPhongQuery
                    ->whereIn('trang_thai', self::TRANG_THAI_XUNG_DOT)
                    ->whereDate('ngay_nhan_phong_du_kien', '<', $ngayTra)
                    ->whereDate('ngay_tra_phong_du_kien', '>', $ngayNhan);
            })
            ->exists();

        return !$coXungDot;
    }

    private function timHoacTaoKhachHang(array $duLieu): KhachHang
    {
        $soDienThoai = $duLieu['so_dien_thoai'] ?? null;
        $email = $duLieu['email'] ?? null;
        $khachHang = KhachHang::timTheoThongTinLienHe($email, $soDienThoai);

        if ($khachHang) {
            $khachHang->fill([
                'ho_ten' => $duLieu['ho_ten'],
                'so_dien_thoai' => $soDienThoai,
                'email' => $email,
                'trang_thai' => 'hoat_dong',
            ]);
            $khachHang->save();

            return $khachHang;
        }

        return KhachHang::query()->create([
            'ma_khach_hang' => KhachHang::taoMaMoi(),
            'ho_ten' => $duLieu['ho_ten'],
            'so_dien_thoai' => $soDienThoai,
            'email' => $email,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function dongBoTrangThaiPhongTheoDatPhong(DatPhong $datPhong): void
    {
        $trangThaiPhongMoi = $this->mapTrangThaiPhong($datPhong->trang_thai);

        foreach ($datPhong->chiTietDatPhong as $chiTiet) {
            if (!$chiTiet->phong) {
                continue;
            }

            if (in_array($datPhong->trang_thai, ['da_tra_phong', 'da_huy'], true) && $this->phongDangCoDatPhongKhac($chiTiet->phong_id, $datPhong->id)) {
                continue;
            }

            $chiTiet->phong->update([
                'trang_thai' => $trangThaiPhongMoi,
            ]);
        }
    }

    private function phongDangCoDatPhongKhac(int $phongId, int $datPhongHienTaiId): bool
    {
        return ChiTietDatPhong::query()
            ->where('phong_id', $phongId)
            ->whereHas('datPhong', function ($query) use ($datPhongHienTaiId) {
                $query
                    ->where('id', '!=', $datPhongHienTaiId)
                    ->whereIn('trang_thai', self::TRANG_THAI_XUNG_DOT);
            })
            ->exists();
    }

    private function taoMaDatPhong(): string
    {
        do {
            $maDatPhong = 'DP' . now()->format('ymdHis') . random_int(10, 99);
        } while (DatPhong::query()->where('ma_dat_phong', $maDatPhong)->exists());

        return $maDatPhong;
    }

    private function taoHoaDonTuDongNeuCan(DatPhong $datPhong): bool
    {
        $daCoHoaDon = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->isNotEmpty();

        if ($daCoHoaDon) {
            return false;
        }

        $tongTienPhong = (float) $datPhong->chiTietDatPhong->sum(function (ChiTietDatPhong $chiTiet) {
            return (float) $chiTiet->gia_phong * (int) $chiTiet->so_dem;
        });

        $tongTien = max(0, $tongTienPhong);
        $trangThaiHoaDon = $tongTien > 0 ? 'chua_thanh_toan' : 'da_thanh_toan';

        HoaDon::query()->create([
            'ma_hoa_don' => $this->taoMaHoaDon(),
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => $tongTienPhong,
            'tong_tien_dich_vu' => 0,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => $tongTien,
            'trang_thai' => $trangThaiHoaDon,
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => auth()->id(),
            'ghi_chu' => 'Hóa đơn được tạo tự động khi đơn đặt phòng chuyển sang trạng thái đã trả phòng.',
        ]);

        return true;
    }

    private function taoMaHoaDon(): string
    {
        do {
            $maHoaDon = 'HD' . now()->format('ymdHis') . random_int(10, 99);
        } while (HoaDon::query()->where('ma_hoa_don', $maHoaDon)->exists());

        return $maHoaDon;
    }
}
