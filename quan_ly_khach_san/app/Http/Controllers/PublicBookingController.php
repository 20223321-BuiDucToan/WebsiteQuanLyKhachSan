<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\KhachHang;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublicBookingController extends Controller
{
    /**
     * Trang public de khach xem phong va dat phong online.
     */
    public function index(Request $request)
    {
        $request->validate([
            'ngay_nhan' => ['nullable', 'date', 'after_or_equal:today'],
            'ngay_tra' => ['nullable', 'date', 'after:ngay_nhan'],
            'so_khach' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $ngayNhan = $request->input('ngay_nhan');
        $ngayTra = $request->input('ngay_tra');
        $soKhach = $request->filled('so_khach') ? (int) $request->input('so_khach') : null;

        try {
            $danhSachPhong = Phong::query()
                ->with('loaiPhong')
                ->where('tinh_trang_hoat_dong', 'hoat_dong')
                ->whereHas('loaiPhong', function ($query) {
                    $query->where('trang_thai', 'hoat_dong');
                })
                ->when($soKhach, function ($query) use ($soKhach) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($soKhach) {
                        $loaiPhongQuery->where('so_nguoi_toi_da', '>=', $soKhach);
                    });
                })
                ->when($ngayNhan && $ngayTra, function ($query) use ($ngayNhan, $ngayTra) {
                    $query->whereDoesntHave('chiTietDatPhong', function ($chiTietQuery) use ($ngayNhan, $ngayTra) {
                        $chiTietQuery->whereHas('datPhong', function ($datPhongQuery) use ($ngayNhan, $ngayTra) {
                            $datPhongQuery
                                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'])
                                ->whereDate('ngay_nhan_phong_du_kien', '<', $ngayTra)
                                ->whereDate('ngay_tra_phong_du_kien', '>', $ngayNhan);
                        });
                    });
                })
                ->orderBy('so_phong')
                ->paginate(9)
                ->withQueryString();
        } catch (QueryException $exception) {
            if (!$this->laLoiThieuBang($exception)) {
                throw $exception;
            }

            $danhSachPhong = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: 9,
                currentPage: $request->integer('page', 1),
                options: [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ],
            );
        }

        $danhSachDonCuaToi = collect();

        if (auth()->check() && auth()->user()->vai_tro === 'khach_hang') {
            $khachHangDangNhap = $this->timKhachHangTuTaiKhoanDangNhap();

            if ($khachHangDangNhap) {
                $danhSachDonCuaToi = DatPhong::query()
                    ->with(['chiTietDatPhong.phong', 'hoaDon.thanhToan'])
                    ->where('khach_hang_id', $khachHangDangNhap->id)
                    ->latest('id')
                    ->take(8)
                    ->get();
            }
        }

        return view('booking.index', [
            'danhSachPhong' => $danhSachPhong,
            'danhSachDonCuaToi' => $danhSachDonCuaToi,
            'boLoc' => [
                'ngay_nhan' => $ngayNhan,
                'ngay_tra' => $ngayTra,
                'so_khach' => $soKhach,
            ],
        ]);
    }

    /**
     * Luu don dat phong online.
     */
    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'phong_id' => ['required', 'integer', 'exists:phong,id'],
            'ho_ten' => ['required', 'string', 'max:100'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:100', 'required_without:so_dien_thoai'],
            'ngay_nhan' => ['required', 'date', 'after_or_equal:today'],
            'ngay_tra' => ['required', 'date', 'after:ngay_nhan'],
            'so_nguoi_lon' => ['required', 'integer', 'min:1', 'max:10'],
            'so_tre_em' => ['nullable', 'integer', 'min:0', 'max:10'],
            'yeu_cau_dac_biet' => ['nullable', 'string', 'max:1000'],
        ]);

        $phong = Phong::query()
            ->with('loaiPhong')
            ->findOrFail($duLieu['phong_id']);

        if ($phong->tinh_trang_hoat_dong !== 'hoat_dong' || !$phong->loaiPhong || $phong->loaiPhong->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages([
                'phong_id' => 'Phong nay hien khong san sang de dat online.',
            ]);
        }

        $soNguoiToiDa = (int) ($phong->loaiPhong->so_nguoi_toi_da ?? 1);
        $tongKhach = (int) $duLieu['so_nguoi_lon'] + (int) ($duLieu['so_tre_em'] ?? 0);

        if ($tongKhach > $soNguoiToiDa) {
            throw ValidationException::withMessages([
                'so_nguoi_lon' => 'Tong so khach vuot qua suc chua toi da cua phong (' . $soNguoiToiDa . ').',
            ]);
        }

        $giaMotDem = (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong->gia_mot_dem ?? 0);
        $ngayNhan = Carbon::parse($duLieu['ngay_nhan'])->startOfDay();
        $ngayTra = Carbon::parse($duLieu['ngay_tra'])->startOfDay();
        $soDem = max(1, $ngayTra->diffInDays($ngayNhan));
        $duLieuKhachDat = $this->duLieuKhachDatTuTaiKhoan($duLieu);

        $datPhong = DB::transaction(function () use ($duLieu, $duLieuKhachDat, $phong, $ngayNhan, $ngayTra, $soDem, $giaMotDem) {
            if (!$this->phongConTrong($phong->id, $ngayNhan->toDateString(), $ngayTra->toDateString())) {
                throw ValidationException::withMessages([
                    'phong_id' => 'Phong da duoc dat trong khoang thoi gian ban chon. Vui long chon phong khac.',
                ]);
            }

            $khachHang = $this->timHoacTaoKhachHang($duLieuKhachDat);

            $datPhong = DatPhong::create([
                'ma_dat_phong' => $this->taoMaDatPhong(),
                'khach_hang_id' => $khachHang->id,
                'nguoi_tao_id' => null,
                'ngay_dat' => now(),
                'ngay_nhan_phong_du_kien' => $ngayNhan->toDateString(),
                'ngay_tra_phong_du_kien' => $ngayTra->toDateString(),
                'so_nguoi_lon' => (int) $duLieu['so_nguoi_lon'],
                'so_tre_em' => (int) ($duLieu['so_tre_em'] ?? 0),
                'trang_thai' => 'cho_xac_nhan',
                'nguon_dat' => 'website',
                'yeu_cau_dac_biet' => $duLieu['yeu_cau_dac_biet'] ?? null,
            ]);

            $datPhong->chiTietDatPhong()->create([
                'phong_id' => $phong->id,
                'gia_phong' => $giaMotDem,
                'so_dem' => $soDem,
                'so_nguoi_lon' => (int) $duLieu['so_nguoi_lon'],
                'so_tre_em' => (int) ($duLieu['so_tre_em'] ?? 0),
                'trang_thai' => 'da_dat',
            ]);

            return $datPhong;
        });

        return redirect()
            ->route('booking.index')
            ->with('success', 'Dat phong thanh cong. Ma dat phong cua ban la ' . $datPhong->ma_dat_phong . '. Chung toi se lien he de xac nhan.');
    }

    private function phongConTrong(int $phongId, string $ngayNhan, string $ngayTra): bool
    {
        $coXungDot = ChiTietDatPhong::query()
            ->where('phong_id', $phongId)
            ->whereHas('datPhong', function ($datPhongQuery) use ($ngayNhan, $ngayTra) {
                $datPhongQuery
                    ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'])
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

        $khachHang = null;

        if ($soDienThoai || $email) {
            $khachHang = KhachHang::query()
                ->where(function ($query) use ($soDienThoai, $email) {
                    if ($soDienThoai) {
                        $query->where('so_dien_thoai', $soDienThoai);
                    }

                    if ($email) {
                        $phuongThuc = $soDienThoai ? 'orWhere' : 'where';
                        $query->{$phuongThuc}('email', $email);
                    }
                })
                ->first();
        }

        if ($khachHang) {
            $khachHang->fill([
                'ho_ten' => $duLieu['ho_ten'],
                'so_dien_thoai' => $soDienThoai,
                'email' => $email,
            ]);
            $khachHang->save();

            return $khachHang;
        }

        return KhachHang::create([
            'ma_khach_hang' => $this->taoMaKhachHang(),
            'ho_ten' => $duLieu['ho_ten'],
            'so_dien_thoai' => $soDienThoai,
            'email' => $email,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function duLieuKhachDatTuTaiKhoan(array $duLieu): array
    {
        $nguoiDung = auth()->user();
        $hoTen = trim((string) ($nguoiDung->ho_ten ?? $duLieu['ho_ten'] ?? ''));
        $soDienThoai = $nguoiDung->so_dien_thoai ?? $duLieu['so_dien_thoai'] ?? null;
        $email = $nguoiDung->email ?? $duLieu['email'] ?? null;

        if (!$email && !$soDienThoai) {
            throw ValidationException::withMessages([
                'email' => 'Tai khoan cua ban can co email hoac so dien thoai de dat phong.',
            ]);
        }

        return [
            'ho_ten' => $hoTen,
            'so_dien_thoai' => $soDienThoai,
            'email' => $email,
        ];
    }

    private function timKhachHangTuTaiKhoanDangNhap(): ?KhachHang
    {
        if (!auth()->check()) {
            return null;
        }

        $nguoiDung = auth()->user();

        return KhachHang::query()
            ->where(function ($query) use ($nguoiDung) {
                if (!empty($nguoiDung->email)) {
                    $query->where('email', $nguoiDung->email);
                }

                if (!empty($nguoiDung->so_dien_thoai)) {
                    $phuongThuc = !empty($nguoiDung->email) ? 'orWhere' : 'where';
                    $query->{$phuongThuc}('so_dien_thoai', $nguoiDung->so_dien_thoai);
                }
            })
            ->first();
    }

    private function taoMaDatPhong(): string
    {
        do {
            $maDatPhong = 'DP' . now()->format('ymdHis') . random_int(10, 99);
        } while (DatPhong::query()->where('ma_dat_phong', $maDatPhong)->exists());

        return $maDatPhong;
    }

    private function taoMaKhachHang(): string
    {
        do {
            $maKhachHang = 'KH' . now()->format('ymdHis') . random_int(10, 99);
        } while (KhachHang::query()->where('ma_khach_hang', $maKhachHang)->exists());

        return $maKhachHang;
    }

    private function laLoiThieuBang(QueryException $exception): bool
    {
        $thongBao = strtolower($exception->getMessage());

        return str_contains($thongBao, 'no such table') || str_contains($thongBao, 'base table or view not found');
    }
}