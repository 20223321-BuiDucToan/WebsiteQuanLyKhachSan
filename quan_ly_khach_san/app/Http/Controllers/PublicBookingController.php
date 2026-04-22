<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Models\ThanhToan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicBookingController extends Controller
{
    /**
     * Trang công khai để khách xem phòng và đặt phòng online.
     */
    public function index(Request $request)
    {
        $request->validate([
            'ngay_nhan' => ['nullable', 'date', 'after_or_equal:today'],
            'ngay_tra' => ['nullable', 'date', 'after_or_equal:ngay_nhan'],
            'so_khach' => ['nullable', 'integer', 'min:1', 'max:10'],
        ], [
            'ngay_nhan.after_or_equal' => 'Ngày nhận phòng phải từ hôm nay trở đi.',
            'ngay_tra.after_or_equal' => 'Ngày trả phòng phải từ ngày nhận phòng trở đi.',
        ]);

        $ngayNhan = $request->input('ngay_nhan');
        $ngayTra = $request->input('ngay_tra');
        $soKhach = $request->filled('so_khach') ? (int) $request->input('so_khach') : null;
        $ngayTraLoc = $ngayTra;

        if ($ngayNhan && $ngayTra && $ngayTra === $ngayNhan) {
            $ngayTraLoc = Carbon::parse($ngayTra)->addDay()->toDateString();
        }

        try {
            $danhSachPhong = Phong::query()
                ->with('loaiPhong')
                ->sanSangChoKhachDat(
                    $ngayNhan ? Carbon::parse($ngayNhan)->startOfDay() : null,
                    $ngayNhan && $ngayTraLoc ? Carbon::parse($ngayTraLoc)->startOfDay() : null
                )
                ->when($soKhach, function ($query) use ($soKhach) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($soKhach) {
                        $loaiPhongQuery->where('so_nguoi_toi_da', '>=', $soKhach);
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
     * Lưu đơn đặt phòng online.
     */
    public function store(Request $request)
    {
        $duLieu = $request->validate([
            'phong_id' => ['required', 'integer', 'exists:phong,id'],
            'ho_ten' => ['required', 'string', 'max:100'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:100', 'required_without:so_dien_thoai'],
            'ngay_nhan' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'ngay_tra' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:ngay_nhan'],
            'so_nguoi_lon' => ['required', 'integer', 'min:1', 'max:10'],
            'so_tre_em' => ['nullable', 'integer', 'min:0', 'max:10'],
            'yeu_cau_dac_biet' => ['nullable', 'string', 'max:1000'],
        ], [
            'phong_id.required' => 'Vui lòng chọn phòng trước khi gửi yêu cầu.',
            'phong_id.exists' => 'Phòng bạn chọn không tồn tại hoặc đã bị xóa.',
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'so_dien_thoai.required_without' => 'Vui lòng nhập số điện thoại hoặc email.',
            'email.required_without' => 'Vui lòng nhập email hoặc số điện thoại.',
            'email.email' => 'Email không đúng định dạng.',
            'ngay_nhan.required' => 'Vui lòng chọn ngày nhận phòng.',
            'ngay_nhan.date' => 'Ngày nhận phòng không hợp lệ.',
            'ngay_nhan.date_format' => 'Ngày nhận phòng phải theo định dạng năm-tháng-ngày (YYYY-MM-DD).',
            'ngay_nhan.after_or_equal' => 'Ngày nhận phòng phải từ hôm nay trở đi.',
            'ngay_tra.required' => 'Vui lòng chọn ngày trả phòng.',
            'ngay_tra.date' => 'Ngày trả phòng không hợp lệ.',
            'ngay_tra.date_format' => 'Ngày trả phòng phải theo định dạng năm-tháng-ngày (YYYY-MM-DD).',
            'ngay_tra.after_or_equal' => 'Ngày trả phòng phải từ ngày nhận phòng trở đi.',
            'so_nguoi_lon.required' => 'Vui lòng nhập số người lớn.',
            'so_nguoi_lon.min' => 'Số người lớn tối thiểu là 1.',
            'so_nguoi_lon.max' => 'Số người lớn tối đa là 10.',
            'so_tre_em.min' => 'Số trẻ em không được âm.',
            'so_tre_em.max' => 'Số trẻ em tối đa là 10.',
        ]);

        $phong = Phong::query()
            ->with('loaiPhong')
            ->findOrFail($duLieu['phong_id']);

        if ($phong->tinh_trang_hoat_dong !== 'hoat_dong' || !$phong->loaiPhong || $phong->loaiPhong->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages([
                'phong_id' => 'Phòng này hiện không sẵn sàng để đặt online.',
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

        if ($ngayTra->equalTo($ngayNhan)) {
            $ngayTra = $ngayNhan->copy()->addDay();
        }

        $soDem = max(1, $ngayTra->diffInDays($ngayNhan));
        $duLieuKhachDat = $this->duLieuKhachDatTuTaiKhoan($duLieu);

        $datPhong = DB::transaction(function () use ($duLieu, $duLieuKhachDat, $phong, $ngayNhan, $ngayTra, $soDem, $giaMotDem) {
            if (!$this->phongConTrong($phong->id, $ngayNhan->toDateString(), $ngayTra->toDateString())) {
                throw ValidationException::withMessages([
                    'phong_id' => 'Phòng này hiện không còn trống để khách đặt. Vui lòng chọn phòng khác.',
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

            $phong->refresh()->dongBoTrangThaiHeThong();

            return $datPhong;
        });

        return redirect()
            ->route('booking.index')
            ->with('success', 'Đặt phòng thành công. Mã đặt phòng của bạn là ' . $datPhong->ma_dat_phong . '. Chúng tôi sẽ liên hệ để xác nhận.');
    }

    public function showTaiKhoan()
    {
        $khachHang = $this->boSungThongTinHoSoKhachHang(
            $this->layKhachHangDangNhap()->load([
                'datPhong' => function ($query) {
                    $query
                        ->with(['chiTietDatPhong.phong', 'hoaDon.thanhToan'])
                        ->latest('id');
                },
            ])
        );

        $danhSachHoaDon = HoaDon::query()
            ->with([
                'datPhong.chiTietDatPhong.phong',
                'thanhToan.nguoiXuLy',
            ])
            ->whereHas('datPhong', function ($query) use ($khachHang) {
                $query->where('khach_hang_id', $khachHang->id);
            })
            ->latest('id')
            ->get()
            ->map(function (HoaDon $hoaDon) {
                $hoaDon->dongBoGiaTriTuDatPhong(false);

                $soTienDaThanhToan = $hoaDon->tinhTongTienDaThu();
                $soTienChoXuLy = $hoaDon->tinhTongTienChoXuLy();

                $hoaDon->setAttribute('so_tien_da_thanh_toan', $soTienDaThanhToan);
                $hoaDon->setAttribute('so_tien_cho_xu_ly', $soTienChoXuLy);
                $hoaDon->setAttribute('so_tien_con_lai', max(0, (float) $hoaDon->tong_tien - $soTienDaThanhToan));

                return $hoaDon;
            });

        $danhSachThanhToan = ThanhToan::query()
            ->with(['hoaDon.datPhong', 'nguoiXuLy'])
            ->whereHas('hoaDon.datPhong', function ($query) use ($khachHang) {
                $query->where('khach_hang_id', $khachHang->id);
            })
            ->latest('id')
            ->take(10)
            ->get();

        $thongKe = [
            'tong_luot_dat' => $khachHang->datPhong->count(),
            'don_sap_toi' => $khachHang->datPhong
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'])
                ->count(),
            'tong_hoa_don' => $danhSachHoaDon->count(),
            'tong_da_thanh_toan' => (float) $danhSachHoaDon->sum('so_tien_da_thanh_toan'),
            'tong_cho_xu_ly' => (float) $danhSachHoaDon->sum('so_tien_cho_xu_ly'),
            'tong_con_lai' => (float) $danhSachHoaDon->sum('so_tien_con_lai'),
            'phan_tram_ho_so' => (int) $khachHang->phan_tram_ho_so,
        ];

        return view('booking.account', [
            'khachHang' => $khachHang,
            'taiKhoan' => auth()->user(),
            'danhSachDatPhong' => $khachHang->datPhong->take(8),
            'danhSachHoaDon' => $danhSachHoaDon->take(8),
            'danhSachThanhToan' => $danhSachThanhToan,
            'thongKe' => $thongKe,
        ]);
    }

    public function updateTaiKhoan(Request $request)
    {
        $khachHang = $this->layKhachHangDangNhap();
        $nguoiDung = $request->user();

        $duLieu = $request->validate([
            'ho_ten' => ['required', 'string', 'max:100'],
            'gioi_tinh' => ['nullable', Rule::in(array_keys(KhachHang::GIOI_TINH))],
            'ngay_sinh' => ['nullable', 'date', 'before:today'],
            'so_dien_thoai' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('khach_hang', 'so_dien_thoai')->ignore($khachHang->id),
                Rule::unique('nguoi_dung', 'so_dien_thoai')->ignore($nguoiDung->id),
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('khach_hang', 'email')->ignore($khachHang->id),
                Rule::unique('nguoi_dung', 'email')->ignore($nguoiDung->id),
            ],
            'quoc_tich' => ['nullable', 'string', 'max:50'],
            'loai_giay_to' => ['nullable', Rule::in(array_keys(KhachHang::LOAI_GIAY_TO))],
            'so_giay_to' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('khach_hang', 'so_giay_to')->ignore($khachHang->id),
            ],
            'dia_chi' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($duLieu, $khachHang, $nguoiDung) {
            $nguoiDung->update([
                'ho_ten' => $duLieu['ho_ten'],
                'email' => $duLieu['email'],
                'so_dien_thoai' => $duLieu['so_dien_thoai'] ?? null,
                'dia_chi' => $duLieu['dia_chi'] ?? null,
            ]);

            $khachHang->update([
                'ho_ten' => $duLieu['ho_ten'],
                'gioi_tinh' => $duLieu['gioi_tinh'] ?? null,
                'ngay_sinh' => $duLieu['ngay_sinh'] ?? null,
                'so_dien_thoai' => $duLieu['so_dien_thoai'] ?? null,
                'email' => $duLieu['email'],
                'quoc_tich' => $duLieu['quoc_tich'] ?? null,
                'loai_giay_to' => $duLieu['loai_giay_to'] ?? null,
                'so_giay_to' => $duLieu['so_giay_to'] ?? null,
                'dia_chi' => $duLieu['dia_chi'] ?? null,
            ]);
        });

        return redirect()
            ->route('booking.account')
            ->with('success', 'Đã cập nhật thông tin khách hàng thành công.');
    }

    public function showHoaDon(HoaDon $hoaDon)
    {
        $khachHangDangNhap = $this->layKhachHangDangNhap();
        $this->xacThucHoaDonThuocKhachDangNhap($hoaDon, $khachHangDangNhap);

        $hoaDon->load([
            'datPhong.khachHang',
            'datPhong.chiTietDatPhong.phong',
            'datPhong.suDungDichVu.dichVu',
            'thanhToan.nguoiTao',
            'thanhToan.nguoiXuLy',
        ]);

        $hoaDon->dongBoGiaTriTuDatPhong(false);

        $soTienDaThanhToan = $hoaDon->tinhTongTienDaThu();
        $soTienChoXuLy = $hoaDon->tinhTongTienChoXuLy();
        $tongTien = (float) $hoaDon->tong_tien;

        return view('booking.hoa_don', [
            'hoaDon' => $hoaDon,
            'soTienDaThanhToan' => $soTienDaThanhToan,
            'soTienChoXuLy' => $soTienChoXuLy,
            'soTienConLai' => max(0, $tongTien - $soTienDaThanhToan),
            'soTienConLaiCoTheGuiYeuCau' => max(0, $tongTien - $soTienDaThanhToan - $soTienChoXuLy),
        ]);
    }

    private function phongConTrong(int $phongId, string $ngayNhan, string $ngayTra): bool
    {
        return Phong::query()
            ->whereKey($phongId)
            ->sanSangChoKhachDat(
                Carbon::parse($ngayNhan)->startOfDay(),
                Carbon::parse($ngayTra)->startOfDay()
            )
            ->exists();
    }

    private function timHoacTaoKhachHang(array $duLieu): KhachHang
    {
        $nguoiDung = auth()->user();

        if ($nguoiDung && $nguoiDung->vai_tro === 'khach_hang') {
            $khachHangTheoTaiKhoan = KhachHang::dongBoTuTaiKhoan($nguoiDung);

            if ($khachHangTheoTaiKhoan) {
                return $khachHangTheoTaiKhoan;
            }
        }

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
                'email' => 'Tài khoản của bạn cần có email hoặc số điện thoại để đặt phòng.',
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

        return KhachHang::timTheoTaiKhoan(auth()->user());
    }

    private function layKhachHangDangNhap(): KhachHang
    {
        $nguoiDung = auth()->user();

        abort_unless($nguoiDung && $nguoiDung->vai_tro === 'khach_hang', 404);

        $khachHang = KhachHang::dongBoTuTaiKhoan($nguoiDung);

        if ($khachHang) {
            return $khachHang;
        }

        return KhachHang::query()->create([
            'nguoi_dung_id' => $nguoiDung->id,
            'ma_khach_hang' => $this->taoMaKhachHang(),
            'ho_ten' => $nguoiDung->ho_ten,
            'so_dien_thoai' => $nguoiDung->so_dien_thoai,
            'email' => $nguoiDung->email,
            'dia_chi' => $nguoiDung->dia_chi,
            'hang_khach_hang' => 'thuong',
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function xacThucHoaDonThuocKhachDangNhap(HoaDon $hoaDon, KhachHang $khachHang): void
    {
        $hoaDon->loadMissing('datPhong');

        abort_unless($hoaDon->datPhong && $hoaDon->datPhong->khach_hang_id === $khachHang->id, 404);
    }

    private function boSungThongTinHoSoKhachHang(KhachHang $khachHang): KhachHang
    {
        $cacMucHoSo = [
            'so_dien_thoai' => 'Số điện thoại',
            'email' => 'Email',
            'ngay_sinh' => 'Ngày sinh',
            'so_giay_to' => 'Giấy tờ',
            'dia_chi' => 'Địa chỉ',
            'quoc_tich' => 'Quốc tịch',
        ];

        $mucConThieu = collect($cacMucHoSo)
            ->filter(fn ($label, $truong) => blank($khachHang->{$truong}))
            ->values()
            ->all();

        $soMucDaCo = count($cacMucHoSo) - count($mucConThieu);
        $phanTramHoSo = (int) round(($soMucDaCo / max(1, count($cacMucHoSo))) * 100);

        $khachHang->setAttribute('thieu_ho_so', $mucConThieu);
        $khachHang->setAttribute('phan_tram_ho_so', $phanTramHoSo);
        $khachHang->setAttribute('co_du_thong_tin_lien_he', filled($khachHang->so_dien_thoai) && filled($khachHang->email));

        return $khachHang;
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
