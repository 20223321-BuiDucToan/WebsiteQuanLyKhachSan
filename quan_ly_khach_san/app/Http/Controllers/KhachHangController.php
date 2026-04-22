<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\NguoiDung;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KhachHangController extends Controller
{
    private const SAP_XEP = [
        'moi_nhat',
        'cu_nhat',
        'ten_az',
        'ten_za',
        'lan_dat_gan_nhat',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'hang_khach_hang' => ['nullable', Rule::in(array_keys(KhachHang::HANG_KHACH_HANG))],
            'trang_thai' => ['nullable', Rule::in(array_keys(KhachHang::TRANG_THAI))],
            'quoc_tich' => ['nullable', 'string', 'max:50'],
            'co_lien_he' => ['nullable', Rule::in(['co', 'thieu'])],
            'co_dat_phong' => ['nullable', Rule::in(['co', 'chua'])],
            'sap_xep' => ['nullable', Rule::in(self::SAP_XEP)],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $hangKhachHang = $request->input('hang_khach_hang');
        $trangThai = $request->input('trang_thai');
        $quocTich = $request->input('quoc_tich');
        $coLienHe = $request->input('co_lien_he');
        $coDatPhong = $request->input('co_dat_phong');
        $sapXep = $request->input('sap_xep', 'moi_nhat');

        $khachHangQuery = $this->taoTruyVanKhachHang(
            $tuKhoa,
            $hangKhachHang,
            $trangThai,
            $quocTich,
            $coLienHe,
            $coDatPhong,
            $sapXep
        );

        $danhSachKhachHang = (clone $khachHangQuery)
            ->paginate(12)
            ->withQueryString();

        $danhSachKhachHang->setCollection(
            $danhSachKhachHang->getCollection()->map(
                fn(KhachHang $khachHang) => $this->boSungDuLieuKhachHang($khachHang)
            )
        );

        $danhSachKhachHangDaLoc = (clone $khachHangQuery)
            ->get()
            ->map(fn(KhachHang $khachHang) => $this->boSungDuLieuKhachHang($khachHang));

        $thongKe = $this->tongHopKhachHang($danhSachKhachHangDaLoc);
        $thongKe['tong_toan_he_thong'] = KhachHang::query()->count();

        $khachHangNoiBat = $danhSachKhachHangDaLoc
            ->filter(fn(KhachHang $khachHang) => $khachHang->dat_phong_count > 0 || $khachHang->tong_doanh_thu > 0)
            ->sortByDesc(fn(KhachHang $khachHang) => $khachHang->tong_doanh_thu + ($khachHang->dat_phong_count * 1000000))
            ->take(5)
            ->values();

        $khachHangCanChamSoc = $danhSachKhachHangDaLoc
            ->filter(fn(KhachHang $khachHang) => $khachHang->thieu_ho_so_count > 0 || !$khachHang->co_thong_tin_lien_he || $khachHang->khong_tuong_tac_lau)
            ->sortByDesc(function (KhachHang $khachHang) {
                return ($khachHang->thieu_ho_so_count * 100)
                    + ($khachHang->khong_tuong_tac_lau ? 40 : 0)
                    + (!$khachHang->co_thong_tin_lien_he ? 25 : 0)
                    + ($khachHang->dat_phong_count === 0 ? 10 : 0);
            })
            ->take(5)
            ->values();

        return view('khach_hang.index', compact(
            'danhSachKhachHang',
            'tuKhoa',
            'hangKhachHang',
            'trangThai',
            'quocTich',
            'coLienHe',
            'coDatPhong',
            'sapXep',
            'thongKe',
            'khachHangNoiBat',
            'khachHangCanChamSoc'
        ));
    }

    public function create()
    {
        return view('khach_hang.create', [
            'khachHang' => new KhachHang(),
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $this->xacThucKhachHang($request);
        $duLieu['ma_khach_hang'] = KhachHang::taoMaMoi();
        $duLieu = array_merge($duLieu, $this->xuLyAnhDaiDien($request));

        $khachHang = KhachHang::query()->create($duLieu);

        return redirect()
            ->route('khach-hang.show', $khachHang)
            ->with('success', 'Thêm khách hàng mới thành công.');
    }

    public function show(KhachHang $khachHang)
    {
        $khachHang->loadCount([
            'datPhong',
            'datPhong as dat_phong_hoan_tat_count' => fn($query) => $query->where('trang_thai', 'da_tra_phong'),
            'datPhong as dat_phong_da_huy_count' => fn($query) => $query->where('trang_thai', 'da_huy'),
        ]);

        $khachHang->load([
            'datPhong' => function ($query) {
                $query
                    ->with(['chiTietDatPhong.phong', 'hoaDon'])
                    ->latest('id');
            },
        ]);

        $khachHang = $this->boSungDuLieuKhachHang($khachHang);

        $thongKeCaNhan = [
            'tong_luot_dat' => (int) $khachHang->dat_phong_count,
            'hoan_tat' => (int) $khachHang->dat_phong_hoan_tat_count,
            'da_huy' => (int) $khachHang->dat_phong_da_huy_count,
            'tong_doanh_thu' => (float) $khachHang->tong_doanh_thu,
            'phan_tram_ho_so' => (int) $khachHang->phan_tram_ho_so,
            'lan_dat_gan_nhat' => $khachHang->lan_dat_phong_gan_nhat_hien_thi,
            'sap_toi' => $khachHang->datPhong
                ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan', 'da_nhan_phong'])
                ->count(),
        ];

        $goiYChamSoc = $this->taoGoiYChamSoc($khachHang);

        return view('khach_hang.show', [
            'khachHang' => $khachHang,
            'thongKeCaNhan' => $thongKeCaNhan,
            'goiYChamSoc' => $goiYChamSoc,
        ]);
    }

    public function edit(KhachHang $khachHang)
    {
        return view('khach_hang.edit', [
            'khachHang' => $khachHang,
        ]);
    }

    public function update(Request $request, KhachHang $khachHang)
    {
        $duLieu = $this->xacThucKhachHang($request, $khachHang);
        $duLieu = array_merge($duLieu, $this->xuLyAnhDaiDien($request, $khachHang));

        DB::transaction(function () use ($khachHang, $duLieu) {
            $khachHang->update($duLieu);
            $this->dongBoTaiKhoanLienKetTheoKhachHang($khachHang->fresh());
        });

        return redirect()
            ->route('khach-hang.show', $khachHang)
            ->with('success', 'Cập nhật khách hàng thành công.');
    }

    public function doiTrangThai(KhachHang $khachHang)
    {
        DB::transaction(function () use ($khachHang) {
            $khachHang->update([
                'trang_thai' => $khachHang->trang_thai === 'hoat_dong' ? 'tam_khoa' : 'hoat_dong',
            ]);

            $this->dongBoTaiKhoanLienKetTheoKhachHang($khachHang->fresh());
        });

        return redirect()
            ->route('khach-hang.index')
            ->with('success', 'Đã đổi trạng thái khách hàng.');
    }

    private function taoTruyVanKhachHang(
        ?string $tuKhoa,
        ?string $hangKhachHang,
        ?string $trangThai,
        ?string $quocTich,
        ?string $coLienHe,
        ?string $coDatPhong,
        string $sapXep
    ) {
        $query = KhachHang::query()
            ->withCount('datPhong')
            ->withCount([
                'datPhong as dat_phong_hoan_tat_count' => fn($query) => $query->where('trang_thai', 'da_tra_phong'),
                'datPhong as dat_phong_da_huy_count' => fn($query) => $query->where('trang_thai', 'da_huy'),
            ])
            ->withMax('datPhong as lan_dat_phong_gan_nhat', 'ngay_dat')
            ->with([
                'datPhong' => function ($query) {
                    $query
                        ->with('hoaDon')
                        ->latest('ngay_dat');
                },
            ])
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_khach_hang', 'like', "%{$tuKhoa}%")
                        ->orWhere('ho_ten', 'like', "%{$tuKhoa}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$tuKhoa}%")
                        ->orWhere('email', 'like', "%{$tuKhoa}%")
                        ->orWhere('so_giay_to', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($hangKhachHang, fn($query) => $query->where('hang_khach_hang', $hangKhachHang))
            ->when($trangThai, fn($query) => $query->where('trang_thai', $trangThai))
            ->when($quocTich, fn($query) => $query->where('quoc_tich', 'like', "%{$quocTich}%"))
            ->when($coLienHe === 'co', function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery
                        ->whereNotNull('so_dien_thoai')
                        ->orWhereNotNull('email');
                });
            })
            ->when($coLienHe === 'thieu', function ($query) {
                $query
                    ->whereNull('so_dien_thoai')
                    ->whereNull('email');
            })
            ->when($coDatPhong === 'co', fn($query) => $query->has('datPhong'))
            ->when($coDatPhong === 'chua', fn($query) => $query->doesntHave('datPhong'));

        return match ($sapXep) {
            'cu_nhat' => $query->oldest('id'),
            'ten_az' => $query->orderBy('ho_ten'),
            'ten_za' => $query->orderByDesc('ho_ten'),
            'lan_dat_gan_nhat' => $query->orderByDesc('lan_dat_phong_gan_nhat')->latest('id'),
            default => $query->latest('id'),
        };
    }

    private function boSungDuLieuKhachHang(KhachHang $khachHang): KhachHang
    {
        $tongDoanhThu = (float) $khachHang->datPhong->sum(function ($datPhong) {
            return (float) $datPhong->hoaDon
                ->where('trang_thai', '!=', 'da_huy')
                ->sum('tong_tien');
        });

        $tyLeHoanTat = $khachHang->dat_phong_count > 0
            ? round(($khachHang->dat_phong_hoan_tat_count / $khachHang->dat_phong_count) * 100, 1)
            : 0;

        $lanDatPhongGanNhat = $khachHang->lan_dat_phong_gan_nhat
            ? Carbon::parse($khachHang->lan_dat_phong_gan_nhat)
            : null;

        $nhomKhachHang = 'moi';

        if ($khachHang->hang_khach_hang === 'kim_cuong' || $tongDoanhThu >= 20000000 || $khachHang->dat_phong_count >= 5) {
            $nhomKhachHang = 'trung_thanh';
        } elseif ($khachHang->dat_phong_count >= 2 || $khachHang->hang_khach_hang === 'vang') {
            $nhomKhachHang = 'tiem_nang';
        }

        $cacMucHoSo = [
            'so_dien_thoai' => 'Số điện thoại',
            'email' => 'Email',
            'ngay_sinh' => 'Ngày sinh',
            'so_giay_to' => 'Giấy tờ',
            'dia_chi' => 'Địa chỉ',
            'quoc_tich' => 'Quốc tịch',
        ];

        $mucConThieu = collect($cacMucHoSo)
            ->filter(fn($label, $truong) => blank($khachHang->{$truong}))
            ->values()
            ->all();

        $soMucDaCo = count($cacMucHoSo) - count($mucConThieu);
        $phanTramHoSo = (int) round(($soMucDaCo / max(1, count($cacMucHoSo))) * 100);
        $khongTuongTacLau = !$lanDatPhongGanNhat || $lanDatPhongGanNhat->lt(now()->subDays(60));

        $khachHang->setAttribute('tong_doanh_thu', $tongDoanhThu);
        $khachHang->setAttribute('ty_le_hoan_tat', $tyLeHoanTat);
        $khachHang->setAttribute('nhom_khach_hang_hien_thi', $nhomKhachHang);
        $khachHang->setAttribute('co_thong_tin_lien_he', filled($khachHang->so_dien_thoai) || filled($khachHang->email));
        $khachHang->setAttribute('co_du_thong_tin_lien_he', filled($khachHang->so_dien_thoai) && filled($khachHang->email));
        $khachHang->setAttribute('thieu_ho_so', $mucConThieu);
        $khachHang->setAttribute('thieu_ho_so_count', count($mucConThieu));
        $khachHang->setAttribute('phan_tram_ho_so', $phanTramHoSo);
        $khachHang->setAttribute('khong_tuong_tac_lau', $khongTuongTacLau);
        $khachHang->setAttribute('lan_dat_phong_gan_nhat_hien_thi', $lanDatPhongGanNhat);

        return $khachHang;
    }

    private function tongHopKhachHang(Collection $danhSachKhachHang): array
    {
        return [
            'tong_hien_thi' => $danhSachKhachHang->count(),
            'hoat_dong' => $danhSachKhachHang->where('trang_thai', 'hoat_dong')->count(),
            'tam_khoa' => $danhSachKhachHang->where('trang_thai', 'tam_khoa')->count(),
            'vip' => $danhSachKhachHang
                ->filter(fn(KhachHang $khachHang) => in_array($khachHang->hang_khach_hang, ['vang', 'kim_cuong'], true))
                ->count(),
            'quay_lai' => $danhSachKhachHang->filter(fn(KhachHang $khachHang) => $khachHang->dat_phong_count >= 2)->count(),
            'tong_luot_dat' => (int) $danhSachKhachHang->sum('dat_phong_count'),
            'tong_doanh_thu' => (float) $danhSachKhachHang->sum('tong_doanh_thu'),
            'co_lien_he' => $danhSachKhachHang->where('co_thong_tin_lien_he', true)->count(),
            'thieu_ho_so' => $danhSachKhachHang->filter(fn(KhachHang $khachHang) => $khachHang->thieu_ho_so_count > 0)->count(),
            'khong_tuong_tac_lau' => $danhSachKhachHang->where('khong_tuong_tac_lau', true)->count(),
            'moi_thang_nay' => $danhSachKhachHang->filter(function (KhachHang $khachHang) {
                return $khachHang->created_at
                    && $khachHang->created_at->isSameMonth(now())
                    && $khachHang->created_at->isSameYear(now());
            })->count(),
        ];
    }

    private function taoGoiYChamSoc(KhachHang $khachHang): array
    {
        $goiY = [];

        if (!$khachHang->co_thong_tin_lien_he) {
            $goiY[] = [
                'icon' => 'fa-phone-slash',
                'class' => 'chip chip-danger',
                'title' => 'Thiếu thông tin liên hệ',
                'description' => 'Nên bổ sung số điện thoại hoặc email để thuận tiện xác nhận đặt phòng.',
            ];
        }

        if ($khachHang->thieu_ho_so_count > 0) {
            $goiY[] = [
                'icon' => 'fa-id-card',
                'class' => 'chip chip-warning',
                'title' => 'Hồ sơ chưa hoàn chỉnh',
                'description' => 'Còn thiếu: ' . implode(', ', array_slice($khachHang->thieu_ho_so, 0, 3)),
            ];
        }

        if ($khachHang->khong_tuong_tac_lau) {
            $goiY[] = [
                'icon' => 'fa-bell-concierge',
                'class' => 'chip chip-neutral',
                'title' => 'Cần tái chăm sóc',
                'description' => 'Khách chưa có tương tác gần đây, phù hợp cho chương trình nhắc quay lại.',
            ];
        }

        if ($khachHang->nhom_khach_hang_hien_thi === 'trung_thanh') {
            $goiY[] = [
                'icon' => 'fa-gem',
                'class' => 'chip chip-info',
                'title' => 'Khách giá trị cao',
                'description' => 'Ưu tiên ưu đãi, nâng hạng và chăm sóc cá nhân hóa để tăng mức độ gắn bó.',
            ];
        }

        if (empty($goiY)) {
            $goiY[] = [
                'icon' => 'fa-circle-check',
                'class' => 'chip chip-success',
                'title' => 'Hồ sơ ổn định',
                'description' => 'Khách hàng đang có dữ liệu tương đối đầy đủ và chưa phát sinh điểm cần xử lý gấp.',
            ];
        }

        return $goiY;
    }

    private function xacThucKhachHang(Request $request, ?KhachHang $khachHang = null): array
    {
        $this->lamSachDuLieuDauVao($request);

        $quyTacSoDienThoai = [
            'nullable',
            'string',
            'max:20',
            Rule::unique('khach_hang', 'so_dien_thoai')->ignore($khachHang?->id),
            Rule::unique('nguoi_dung', 'so_dien_thoai')->ignore($khachHang?->nguoi_dung_id),
        ];

        $quyTacEmail = [
            'email',
            'max:100',
            Rule::unique('khach_hang', 'email')->ignore($khachHang?->id),
            Rule::unique('nguoi_dung', 'email')->ignore($khachHang?->nguoi_dung_id),
        ];

        if ($khachHang?->nguoi_dung_id) {
            array_unshift($quyTacEmail, 'required');
        } else {
            array_unshift($quyTacEmail, 'nullable');
        }

        return $request->validate([
            'ho_ten' => ['required', 'string', 'max:100'],
            'gioi_tinh' => ['nullable', Rule::in(array_keys(KhachHang::GIOI_TINH))],
            'ngay_sinh' => ['nullable', 'date', 'before:today'],
            'so_dien_thoai' => $quyTacSoDienThoai,
            'email' => $quyTacEmail,
            'so_giay_to' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('khach_hang', 'so_giay_to')->ignore($khachHang?->id),
            ],
            'loai_giay_to' => ['nullable', Rule::in(array_keys(KhachHang::LOAI_GIAY_TO))],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'quoc_tich' => ['nullable', 'string', 'max:50'],
            'hang_khach_hang' => ['required', Rule::in(array_keys(KhachHang::HANG_KHACH_HANG))],
            'trang_thai' => ['required', Rule::in(array_keys(KhachHang::TRANG_THAI))],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'xoa_anh_dai_dien' => ['nullable', 'boolean'],
        ]);
    }

    private function lamSachDuLieuDauVao(Request $request): void
    {
        $truongVanBan = [
            'ho_ten',
            'so_dien_thoai',
            'email',
            'so_giay_to',
            'dia_chi',
            'quoc_tich',
            'ghi_chu',
        ];

        foreach ($truongVanBan as $truong) {
            $giaTri = $request->input($truong);

            if (!is_string($giaTri)) {
                continue;
            }

            $giaTri = trim($giaTri);

            if ($truong === 'ho_ten') {
                $giaTri = preg_replace('/\s+/u', ' ', $giaTri);
            }

            if ($truong === 'email') {
                $giaTri = mb_strtolower($giaTri);
            }

            $request->merge([
                $truong => $giaTri === '' ? null : $giaTri,
            ]);
        }
    }

    private function xuLyAnhDaiDien(Request $request, ?KhachHang $khachHang = null): array
    {
        $duLieu = [];

        if ($request->boolean('xoa_anh_dai_dien') && $khachHang?->anh_dai_dien) {
            $this->xoaAnhDaiDien($khachHang->anh_dai_dien);
            $duLieu['anh_dai_dien'] = null;
        }

        if (!$request->hasFile('anh_dai_dien')) {
            return $duLieu;
        }

        if ($khachHang?->anh_dai_dien) {
            $this->xoaAnhDaiDien($khachHang->anh_dai_dien);
        }

        $tep = $request->file('anh_dai_dien');
        $tenTep = 'khach-hang-' . Str::slug((string) $request->input('ho_ten', 'ho-so')) . '-' . now()->format('YmdHis') . '.' . $tep->getClientOriginalExtension();
        $thuMuc = public_path('uploads/khach-hang');

        if (!File::isDirectory($thuMuc)) {
            File::makeDirectory($thuMuc, 0755, true);
        }

        $tep->move($thuMuc, $tenTep);
        $duLieu['anh_dai_dien'] = 'uploads/khach-hang/' . $tenTep;

        return $duLieu;
    }

    private function xoaAnhDaiDien(?string $duongDan): void
    {
        if (!$duongDan) {
            return;
        }

        $duongDanDayDu = public_path($duongDan);

        if (File::exists($duongDanDayDu)) {
            File::delete($duongDanDayDu);
        }
    }

    private function dongBoKhachHangTuTaiKhoan(): void
    {
        $danhSachTaiKhoanKhachHang = NguoiDung::query()
            ->where('vai_tro', 'khach_hang')
            ->get();

        foreach ($danhSachTaiKhoanKhachHang as $taiKhoanKhachHang) {
            KhachHang::dongBoTuTaiKhoan($taiKhoanKhachHang);
        }
    }

    private function dongBoTaiKhoanLienKetTheoKhachHang(KhachHang $khachHang): void
    {
        $khachHang->loadMissing('nguoiDung');

        if (! $khachHang->nguoiDung) {
            return;
        }

        $khachHang->nguoiDung->update([
            'ho_ten' => $khachHang->ho_ten,
            'email' => $khachHang->email,
            'so_dien_thoai' => $khachHang->so_dien_thoai,
            'dia_chi' => $khachHang->dia_chi,
            'trang_thai' => $khachHang->trang_thai === 'tam_khoa' ? 'tam_khoa' : 'hoat_dong',
        ]);
    }
}
