<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\LoaiPhong;
use App\Models\Phong;
use App\Models\ThanhToan;
use App\Support\DatPhongKhachDatQuaHan;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PublicBookingController extends Controller
{
    /**
     * Trang công khai để khách xem phòng và đặt phòng online.
     */
    public function index(Request $request)
    {
        $tuyChonSapXep = $this->layTuyChonSapXepPortal();
        $tuyChonTienNghi = $this->layTuyChonTienNghiPortal();

        $request->validate([
            'ngay_nhan' => ['nullable', 'date', 'after_or_equal:today'],
            'ngay_tra' => ['nullable', 'date', 'after_or_equal:ngay_nhan'],
            'so_khach' => ['nullable', 'integer', 'min:1', 'max:10'],
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'loai_phong_id' => ['nullable', 'integer', 'exists:loai_phong,id'],
            'gia_tu' => ['nullable', 'numeric', 'min:0'],
            'gia_den' => ['nullable', 'numeric', 'min:0', 'gte:gia_tu'],
            'so_giuong' => ['nullable', 'integer', 'min:1', 'max:10'],
            'loai_giuong' => ['nullable', 'string', 'max:50'],
            'so_phong_tam' => ['nullable', 'integer', 'min:1', 'max:10'],
            'dien_tich_tu' => ['nullable', 'numeric', 'min:0'],
            'dien_tich_den' => ['nullable', 'numeric', 'min:0', 'gte:dien_tich_tu'],
            'tang_tu' => ['nullable', 'integer', 'min:1', 'max:100'],
            'tang_den' => ['nullable', 'integer', 'min:1', 'max:100', 'gte:tang_tu'],
            'co_anh' => ['nullable', 'boolean'],
            'co_ban_cong' => ['nullable', 'boolean'],
            'co_bep_rieng' => ['nullable', 'boolean'],
            'co_huong_bien' => ['nullable', 'boolean'],
            'sap_xep' => ['nullable', Rule::in(array_keys($tuyChonSapXep))],
        ], [
            'ngay_nhan.after_or_equal' => 'Ngày nhận phòng phải từ hôm nay trở đi.',
            'ngay_tra.after_or_equal' => 'Ngày trả phòng phải từ ngày nhận phòng trở đi.',
        ]);

        $ngayNhan = $request->input('ngay_nhan');
        $ngayTra = $request->input('ngay_tra');
        $soKhach = $request->filled('so_khach') ? (int) $request->input('so_khach') : null;
        $tuKhoa = trim((string) $request->input('tu_khoa', ''));
        $loaiPhongId = $request->filled('loai_phong_id') ? (int) $request->input('loai_phong_id') : null;
        $giaTu = $request->filled('gia_tu') ? (float) $request->input('gia_tu') : null;
        $giaDen = $request->filled('gia_den') ? (float) $request->input('gia_den') : null;
        $soGiuong = $request->filled('so_giuong') ? (int) $request->input('so_giuong') : null;
        $loaiGiuong = trim((string) $request->input('loai_giuong', ''));
        $loaiGiuong = $loaiGiuong !== '' ? $loaiGiuong : null;
        $soPhongTam = $request->filled('so_phong_tam') ? (int) $request->input('so_phong_tam') : null;
        $dienTichTu = $request->filled('dien_tich_tu') ? (float) $request->input('dien_tich_tu') : null;
        $dienTichDen = $request->filled('dien_tich_den') ? (float) $request->input('dien_tich_den') : null;
        $tangTu = $request->filled('tang_tu') ? (int) $request->input('tang_tu') : null;
        $tangDen = $request->filled('tang_den') ? (int) $request->input('tang_den') : null;
        $coAnh = $request->boolean('co_anh');
        $coBanCong = $request->boolean('co_ban_cong');
        $coBepRieng = $request->boolean('co_bep_rieng');
        $coHuongBien = $request->boolean('co_huong_bien');
        $sapXep = $request->input('sap_xep', 'so_phong');
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
                ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                    $query->where(function ($innerQuery) use ($tuKhoa) {
                        $innerQuery
                            ->where('so_phong', 'like', "%{$tuKhoa}%")
                            ->orWhere('ma_phong', 'like', "%{$tuKhoa}%")
                            ->orWhereHas('loaiPhong', function ($loaiPhongQuery) use ($tuKhoa) {
                                $loaiPhongQuery
                                    ->where('ten_loai_phong', 'like', "%{$tuKhoa}%")
                                    ->orWhere('mo_ta', 'like', "%{$tuKhoa}%")
                                    ->orWhere('loai_giuong', 'like', "%{$tuKhoa}%");
                            });
                    });
                })
                ->when($soKhach, function ($query) use ($soKhach) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($soKhach) {
                        $loaiPhongQuery->where('so_nguoi_toi_da', '>=', $soKhach);
                    });
                })
                ->when($loaiPhongId, fn ($query) => $query->where('loai_phong_id', $loaiPhongId))
                ->when($soGiuong, function ($query) use ($soGiuong) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($soGiuong) {
                        $loaiPhongQuery->where('so_giuong', '>=', $soGiuong);
                    });
                })
                ->when($loaiGiuong, function ($query) use ($loaiGiuong) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($loaiGiuong) {
                        $loaiPhongQuery->where('loai_giuong', $loaiGiuong);
                    });
                })
                ->when($soPhongTam, function ($query) use ($soPhongTam) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($soPhongTam) {
                        $loaiPhongQuery->where('so_phong_tam', '>=', $soPhongTam);
                    });
                })
                ->when($dienTichTu, function ($query) use ($dienTichTu) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($dienTichTu) {
                        $loaiPhongQuery->where('dien_tich', '>=', $dienTichTu);
                    });
                })
                ->when($dienTichDen, function ($query) use ($dienTichDen) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($dienTichDen) {
                        $loaiPhongQuery->where('dien_tich', '<=', $dienTichDen);
                    });
                })
                ->when($tangTu, fn ($query) => $query->where('tang', '>=', $tangTu))
                ->when($tangDen, fn ($query) => $query->where('tang', '<=', $tangDen))
                ->when($coAnh, fn ($query) => $query->coAnh())
                ->when($request->has('co_ban_cong'), function ($query) use ($coBanCong) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($coBanCong) {
                        $loaiPhongQuery->where('co_ban_cong', $coBanCong);
                    });
                })
                ->when($request->has('co_bep_rieng'), function ($query) use ($coBepRieng) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($coBepRieng) {
                        $loaiPhongQuery->where('co_bep_rieng', $coBepRieng);
                    });
                })
                ->when($request->has('co_huong_bien'), function ($query) use ($coHuongBien) {
                    $query->whereHas('loaiPhong', function ($loaiPhongQuery) use ($coHuongBien) {
                        $loaiPhongQuery->where('co_huong_bien', $coHuongBien);
                    });
                })
                ->when($giaTu !== null, function ($query) use ($giaTu) {
                    $this->apDungBoLocGiaPhong($query, '>=', $giaTu);
                })
                ->when($giaDen !== null, function ($query) use ($giaDen) {
                    $this->apDungBoLocGiaPhong($query, '<=', $giaDen);
                });

            $this->apDungSapXepPhong($danhSachPhong, $sapXep);

            $danhSachPhong = $danhSachPhong
                ->paginate(9)
                ->withQueryString();

            $danhSachLoaiPhong = LoaiPhong::query()
                ->where('trang_thai', 'hoat_dong')
                ->orderBy('ten_loai_phong')
                ->get();
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

            $danhSachLoaiPhong = collect();
        }

        $boLoc = [
            'ngay_nhan' => $ngayNhan,
            'ngay_tra' => $ngayTra,
            'so_khach' => $soKhach,
            'tu_khoa' => $tuKhoa,
            'loai_phong_id' => $loaiPhongId,
            'gia_tu' => $giaTu,
            'gia_den' => $giaDen,
            'so_giuong' => $soGiuong,
            'loai_giuong' => $loaiGiuong,
            'so_phong_tam' => $soPhongTam,
            'dien_tich_tu' => $dienTichTu,
            'dien_tich_den' => $dienTichDen,
            'tang_tu' => $tangTu,
            'tang_den' => $tangDen,
            'co_anh' => $coAnh,
            'co_ban_cong' => $coBanCong,
            'co_bep_rieng' => $coBepRieng,
            'co_huong_bien' => $coHuongBien,
            'sap_xep' => $sapXep,
        ];

        $danhSachLoaiGiuong = $this->taoDanhSachLoaiGiuong($danhSachLoaiPhong);
        $soBoLocNangCao = $this->demBoLocNangCao($request, $boLoc);
        $tomTatBoLoc = $this->taoTomTatBoLocPortal($danhSachLoaiPhong, $boLoc);
        $danhSachDonCuaToi = collect();

        if (auth()->check() && auth()->user()->vai_tro === 'khach_hang') {
            $khachHangDangNhap = $this->timKhachHangTuTaiKhoanDangNhap();

            if ($khachHangDangNhap) {
                $danhSachDonCuaToi = DatPhong::query()
                    ->with(['chiTietDatPhong.phong', 'hoaDon.thanhToan'])
                    ->where('khach_hang_id', $khachHangDangNhap->id)
                    ->latest('id')
                    ->take(8)
                    ->get()
                    ->map(fn (DatPhong $datPhong) => $this->boSungThongTinTuDongXuLyKhachDat($datPhong));
            }
        }

        return view('booking.index', [
            'danhSachPhong' => $danhSachPhong,
            'danhSachLoaiPhong' => $danhSachLoaiPhong,
            'danhSachLoaiGiuong' => $danhSachLoaiGiuong,
            'danhSachDonCuaToi' => $danhSachDonCuaToi,
            'tuyChonSapXep' => $tuyChonSapXep,
            'tuyChonTienNghi' => $tuyChonTienNghi,
            'boLoc' => $boLoc,
            'soBoLocNangCao' => $soBoLocNangCao,
            'coMoBoLocNangCao' => $soBoLocNangCao > 0,
            'tomTatBoLoc' => $tomTatBoLoc,
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

        $hanTuDongXuLy = DatPhongKhachDatQuaHan::layHanXuLy($datPhong);
        $thongBao = 'Đặt phòng thành công. Mã đặt phòng của bạn là ' . $datPhong->ma_dat_phong . '. Chúng tôi sẽ liên hệ để xác nhận.';

        if ($hanTuDongXuLy) {
            $thongBao .= ' Nếu quá ' . $hanTuDongXuLy->format('H:i d/m/Y') . ' mà chưa đến, hệ thống sẽ tự động xử lý đơn theo trạng thái xác nhận.';
        }

        return redirect()
            ->route('booking.index')
            ->with('success', $thongBao);
    }

    public function showTaiKhoan()
    {
        $khachHang = $this->layKhachHangDaTaiDuLieuPortal();
        $danhSachHoaDon = $this->layDanhSachHoaDonKhachHang($khachHang);
        $thongKe = $this->taoThongKePortalKhachHang($khachHang, $danhSachHoaDon);
        $hoaDonCanChuY = $danhSachHoaDon
            ->filter(function (HoaDon $hoaDon) {
                return (float) $hoaDon->so_tien_con_lai > 0
                    || (float) $hoaDon->so_tien_cho_xu_ly > 0;
            })
            ->take(3)
            ->values();

        return view('booking.account', [
            'khachHang' => $khachHang,
            'taiKhoan' => auth()->user(),
            'danhSachDatPhong' => $khachHang->datPhong->take(8),
            'hoaDonCanChuY' => $hoaDonCanChuY,
            'thongKe' => $thongKe,
        ]);
    }

    public function showThanhToan()
    {
        $khachHang = $this->layKhachHangDaTaiDuLieuPortal();
        $danhSachHoaDon = $this->layDanhSachHoaDonKhachHang($khachHang);
        $danhSachThanhToan = $this->layDanhSachThanhToanKhachHang($khachHang);
        $thongKe = $this->taoThongKePortalKhachHang($khachHang, $danhSachHoaDon);

        return view('booking.payments', [
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

    public function showHoaDon(Request $request, HoaDon $hoaDon)
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
        $soTienConLai = max(0, $tongTien - $soTienDaThanhToan);
        $soTienConLaiCoTheGuiYeuCau = max(0, $tongTien - $soTienDaThanhToan - $soTienChoXuLy);
        $goiYDatCoc = $hoaDon->datPhong?->tinhTienCocGoiY() ?? 0;
        $soTienConThieuCoc = max(0, $goiYDatCoc - $soTienDaThanhToan - $soTienChoXuLy);
        $coTheDatCoc = $hoaDon->datPhong
            && in_array(
                $hoaDon->datPhong->trang_thai,
                [DatPhong::TRANG_THAI_CHO_XAC_NHAN, DatPhong::TRANG_THAI_DA_XAC_NHAN],
                true
            )
            && $goiYDatCoc > 0
            && $soTienConThieuCoc > 0;
        $cheDoMacDinhThanhToan = $request->input('che_do') === 'coc' && $coTheDatCoc
            ? 'coc_phong'
            : 'thanh_toan_them';

        return view('booking.hoa_don', [
            'hoaDon' => $hoaDon,
            'soTienDaThanhToan' => $soTienDaThanhToan,
            'soTienChoXuLy' => $soTienChoXuLy,
            'soTienConLai' => $soTienConLai,
            'soTienConLaiCoTheGuiYeuCau' => $soTienConLaiCoTheGuiYeuCau,
            'goiYDatCoc' => $goiYDatCoc,
            'soTienConThieuCoc' => $soTienConThieuCoc,
            'coTheDatCoc' => $coTheDatCoc,
            'cheDoMacDinhThanhToan' => $cheDoMacDinhThanhToan,
            'soTienMacDinhChoCoc' => min($soTienConLaiCoTheGuiYeuCau, $soTienConThieuCoc),
            'soTienMacDinhChoThanhToan' => $soTienConLaiCoTheGuiYeuCau,
        ]);
    }

    private function layKhachHangDaTaiDuLieuPortal(): KhachHang
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

        $khachHang->setRelation(
            'datPhong',
            $khachHang->datPhong->map(fn (DatPhong $datPhong) => $this->boSungThongTinTuDongXuLyKhachDat($datPhong))
        );

        return $khachHang;
    }

    private function layDanhSachHoaDonKhachHang(KhachHang $khachHang): Collection
    {
        return HoaDon::query()
            ->with([
                'datPhong.chiTietDatPhong.phong',
                'datPhong.suDungDichVu',
                'thanhToan.nguoiXuLy',
            ])
            ->whereHas('datPhong', function ($query) use ($khachHang) {
                $query->where('khach_hang_id', $khachHang->id);
            })
            ->latest('id')
            ->get()
            ->map(fn (HoaDon $hoaDon) => $this->ganChiSoThanhToanChoHoaDon($hoaDon));
    }

    private function layDanhSachThanhToanKhachHang(KhachHang $khachHang): Collection
    {
        return ThanhToan::query()
            ->with(['hoaDon.datPhong', 'nguoiXuLy'])
            ->whereHas('hoaDon.datPhong', function ($query) use ($khachHang) {
                $query->where('khach_hang_id', $khachHang->id);
            })
            ->latest('id')
            ->take(10)
            ->get();
    }

    private function taoThongKePortalKhachHang(KhachHang $khachHang, Collection $danhSachHoaDon): array
    {
        return [
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
    }

    private function ganChiSoThanhToanChoHoaDon(HoaDon $hoaDon): HoaDon
    {
        $hoaDon->dongBoGiaTriTuDatPhong(false);

        $soTienDaThanhToan = $hoaDon->tinhTongTienDaThu();
        $soTienChoXuLy = $hoaDon->tinhTongTienChoXuLy();
        $goiYDatCoc = $hoaDon->datPhong?->tinhTienCocGoiY() ?? 0;
        $soTienConThieuCoc = max(0, $goiYDatCoc - $soTienDaThanhToan - $soTienChoXuLy);
        $coTheDatCoc = $hoaDon->datPhong
            && in_array(
                $hoaDon->datPhong->trang_thai,
                [DatPhong::TRANG_THAI_CHO_XAC_NHAN, DatPhong::TRANG_THAI_DA_XAC_NHAN],
                true
            )
            && $goiYDatCoc > 0
            && $soTienConThieuCoc > 0;

        $hoaDon->setAttribute('so_tien_da_thanh_toan', $soTienDaThanhToan);
        $hoaDon->setAttribute('so_tien_cho_xu_ly', $soTienChoXuLy);
        $hoaDon->setAttribute('so_tien_con_lai', max(0, (float) $hoaDon->tong_tien - $soTienDaThanhToan));
        $hoaDon->setAttribute('goi_y_tien_coc', $goiYDatCoc);
        $hoaDon->setAttribute('so_tien_con_thieu_coc', $soTienConThieuCoc);
        $hoaDon->setAttribute('co_the_dat_coc', $coTheDatCoc);

        return $hoaDon;
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

    private function boSungThongTinTuDongXuLyKhachDat(DatPhong $datPhong): DatPhong
    {
        foreach (DatPhongKhachDatQuaHan::taoChiSoHienThi($datPhong) as $thuocTinh => $giaTri) {
            $datPhong->setAttribute($thuocTinh, $giaTri);
        }

        return $datPhong;
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

    private function apDungBoLocGiaPhong($query, string $toanTu, float $giaTri): void
    {
        $query->where(function ($giaQuery) use ($toanTu, $giaTri) {
            $giaQuery
                ->where('gia_mac_dinh', $toanTu, $giaTri)
                ->orWhere(function ($duPhongQuery) use ($toanTu, $giaTri) {
                    $duPhongQuery
                        ->whereNull('gia_mac_dinh')
                        ->whereHas('loaiPhong', function ($loaiPhongQuery) use ($toanTu, $giaTri) {
                            $loaiPhongQuery->where('gia_mot_dem', $toanTu, $giaTri);
                        });
                });
        });
    }

    private function apDungSapXepPhong($query, string $sapXep): void
    {
        $bieuThucGia = "COALESCE(phong.gia_mac_dinh, (SELECT loai_phong.gia_mot_dem FROM loai_phong WHERE loai_phong.id = phong.loai_phong_id))";
        $bieuThucDienTich = "(SELECT loai_phong.dien_tich FROM loai_phong WHERE loai_phong.id = phong.loai_phong_id)";
        $bieuThucSucChua = "(SELECT loai_phong.so_nguoi_toi_da FROM loai_phong WHERE loai_phong.id = phong.loai_phong_id)";

        match ($sapXep) {
            'gia_tang' => $query->orderByRaw($bieuThucGia . ' asc')->orderBy('so_phong'),
            'gia_giam' => $query->orderByRaw($bieuThucGia . ' desc')->orderBy('so_phong'),
            'dien_tich_giam' => $query->orderByRaw($bieuThucDienTich . ' desc')->orderBy('so_phong'),
            'suc_chua_giam' => $query->orderByRaw($bieuThucSucChua . ' desc')->orderBy('so_phong'),
            default => $query->orderBy('so_phong'),
        };
    }

    private function taoDanhSachLoaiGiuong(Collection $danhSachLoaiPhong): Collection
    {
        return $danhSachLoaiPhong
            ->pluck('loai_giuong')
            ->filter(fn ($loaiGiuong) => is_string($loaiGiuong) && trim($loaiGiuong) !== '')
            ->map(fn (string $loaiGiuong) => trim($loaiGiuong))
            ->unique()
            ->sort()
            ->values();
    }

    private function demBoLocNangCao(Request $request, array $boLoc): int
    {
        return collect([
            $boLoc['loai_phong_id'] !== null,
            $boLoc['gia_tu'] !== null,
            $boLoc['gia_den'] !== null,
            $boLoc['so_giuong'] !== null,
            $boLoc['loai_giuong'] !== null,
            $boLoc['so_phong_tam'] !== null,
            $boLoc['dien_tich_tu'] !== null,
            $boLoc['dien_tich_den'] !== null,
            $boLoc['tang_tu'] !== null,
            $boLoc['tang_den'] !== null,
            $boLoc['co_anh'],
            $request->has('co_ban_cong'),
            $request->has('co_bep_rieng'),
            $request->has('co_huong_bien'),
        ])->filter()->count();
    }

    private function taoTomTatBoLoc(Collection $danhSachLoaiPhong, array $boLoc): array
    {
        $tomTat = [];
        $tenLoaiPhong = $danhSachLoaiPhong
            ->firstWhere('id', $boLoc['loai_phong_id'])
            ?->ten_loai_phong;

        if ($boLoc['tu_khoa'] !== '') {
            $tomTat[] = 'Từ khóa: ' . $boLoc['tu_khoa'];
        }

        if ($boLoc['ngay_nhan']) {
            $tomTat[] = 'Nhận phòng ' . Carbon::parse($boLoc['ngay_nhan'])->format('d/m/Y');
        }

        if ($boLoc['ngay_tra']) {
            $tomTat[] = 'Trả phòng ' . Carbon::parse($boLoc['ngay_tra'])->format('d/m/Y');
        }

        if ($boLoc['so_khach']) {
            $tomTat[] = $boLoc['so_khach'] . ' khách';
        }

        if ($tenLoaiPhong) {
            $tomTat[] = 'Loại: ' . $tenLoaiPhong;
        }

        if ($boLoc['gia_tu'] !== null) {
            $tomTat[] = 'Giá từ ' . number_format($boLoc['gia_tu'], 0, ',', '.') . ' VND';
        }

        if ($boLoc['gia_den'] !== null) {
            $tomTat[] = 'Giá đến ' . number_format($boLoc['gia_den'], 0, ',', '.') . ' VND';
        }

        if ($boLoc['so_giuong'] !== null) {
            $tomTat[] = 'Từ ' . $boLoc['so_giuong'] . ' giường';
        }

        if ($boLoc['loai_giuong']) {
            $tomTat[] = 'Giường ' . $boLoc['loai_giuong'];
        }

        if ($boLoc['so_phong_tam'] !== null) {
            $tomTat[] = 'Từ ' . $boLoc['so_phong_tam'] . ' phòng tắm';
        }

        if ($boLoc['dien_tich_tu'] !== null) {
            $tomTat[] = 'Từ ' . number_format($boLoc['dien_tich_tu'], 0, ',', '.') . ' m²';
        }

        if ($boLoc['tang'] !== null) {
            $tomTat[] = 'Tầng ' . $boLoc['tang'];
        }

        if ($boLoc['co_anh']) {
            $tomTat[] = 'Có ảnh';
        }

        if ($boLoc['co_anh']) {
            $tomTat[] = 'Có ảnh';
        }

        if ($boLoc['co_ban_cong']) {
            $tomTat[] = 'Có ban công';
        }

        if ($boLoc['co_bep_rieng']) {
            $tomTat[] = 'Có bếp riêng';
        }

        if ($boLoc['co_huong_bien']) {
            $tomTat[] = 'Hướng biển';
        }

        if (($boLoc['sap_xep'] ?? 'so_phong') !== 'so_phong') {
            $tomTat[] = 'Sắp xếp: ' . $this->layNhanSapXep($boLoc['sap_xep']);
        }

        return $tomTat;
    }

    private function layNhanSapXep(string $sapXep): string
    {
        return match ($sapXep) {
            'gia_tang' => 'Giá thấp đến cao',
            'gia_giam' => 'Giá cao đến thấp',
            'dien_tich_giam' => 'Diện tích lớn trước',
            default => 'Theo số phòng',
        };
    }

    private function taoTomTatBoLocPortal(Collection $danhSachLoaiPhong, array $boLoc): array
    {
        $tomTat = [];
        $tenLoaiPhong = $danhSachLoaiPhong
            ->firstWhere('id', $boLoc['loai_phong_id'])
            ?->ten_loai_phong;

        if ($boLoc['tu_khoa'] !== '') {
            $tomTat[] = 'Từ khóa: ' . $boLoc['tu_khoa'];
        }

        if ($boLoc['ngay_nhan']) {
            $tomTat[] = 'Nhận phòng ' . Carbon::parse($boLoc['ngay_nhan'])->format('d/m/Y');
        }

        if ($boLoc['ngay_tra']) {
            $tomTat[] = 'Trả phòng ' . Carbon::parse($boLoc['ngay_tra'])->format('d/m/Y');
        }

        if ($boLoc['so_khach']) {
            $tomTat[] = $boLoc['so_khach'] . ' khách';
        }

        if ($tenLoaiPhong) {
            $tomTat[] = 'Loại: ' . $tenLoaiPhong;
        }

        if ($boLoc['gia_tu'] !== null) {
            $tomTat[] = 'Giá từ ' . number_format($boLoc['gia_tu'], 0, ',', '.') . ' VND';
        }

        if ($boLoc['gia_den'] !== null) {
            $tomTat[] = 'Giá đến ' . number_format($boLoc['gia_den'], 0, ',', '.') . ' VND';
        }

        if ($boLoc['so_giuong'] !== null) {
            $tomTat[] = 'Từ ' . $boLoc['so_giuong'] . ' giường';
        }

        if ($boLoc['loai_giuong']) {
            $tomTat[] = 'Giường ' . $boLoc['loai_giuong'];
        }

        if ($boLoc['so_phong_tam'] !== null) {
            $tomTat[] = 'Từ ' . $boLoc['so_phong_tam'] . ' phòng tắm';
        }

        if ($boLoc['dien_tich_tu'] !== null || $boLoc['dien_tich_den'] !== null) {
            $tomTat[] = $this->taoNhanKhoangPortal('Diện tích', $boLoc['dien_tich_tu'], $boLoc['dien_tich_den'], 'm²');
        }

        if ($boLoc['tang_tu'] !== null || $boLoc['tang_den'] !== null) {
            $tomTat[] = $this->taoNhanKhoangPortal('Tầng', $boLoc['tang_tu'], $boLoc['tang_den']);
        }

        if ($boLoc['co_ban_cong']) {
            $tomTat[] = 'Có ban công';
        }

        if ($boLoc['co_bep_rieng']) {
            $tomTat[] = 'Có bếp riêng';
        }

        if ($boLoc['co_huong_bien']) {
            $tomTat[] = 'Hướng biển';
        }

        if (($boLoc['sap_xep'] ?? 'so_phong') !== 'so_phong') {
            $tomTat[] = 'Sắp xếp: ' . $this->layNhanSapXepPortal($boLoc['sap_xep']);
        }

        return $tomTat;
    }

    private function layNhanSapXepPortal(string $sapXep): string
    {
        return match ($sapXep) {
            'gia_tang' => 'Giá thấp đến cao',
            'gia_giam' => 'Giá cao đến thấp',
            'dien_tich_giam' => 'Diện tích lớn trước',
            'suc_chua_giam' => 'Sức chứa lớn trước',
            default => 'Theo số phòng',
        };
    }

    private function taoNhanKhoangPortal(string $nhan, float|int|null $tu, float|int|null $den, ?string $donVi = null): string
    {
        $dinhDang = function (float|int $giaTri) use ($donVi): string {
            $noiDung = number_format((float) $giaTri, 0, ',', '.');

            return $donVi ? $noiDung . ' ' . $donVi : $noiDung;
        };

        if ($tu !== null && $den !== null) {
            return $nhan . ' ' . $dinhDang($tu) . ' - ' . $dinhDang($den);
        }

        if ($tu !== null) {
            return $nhan . ' từ ' . $dinhDang($tu);
        }

        return $nhan . ' đến ' . $dinhDang((float) $den);
    }

    private function layTuyChonSapXepPortal(): array
    {
        return [
            'so_phong' => 'Theo số phòng',
            'gia_tang' => 'Giá thấp đến cao',
            'gia_giam' => 'Giá cao đến thấp',
            'dien_tich_giam' => 'Diện tích lớn trước',
            'suc_chua_giam' => 'Sức chứa lớn trước',
        ];
    }

    private function layTuyChonTienNghiPortal(): array
    {
        return [
            'co_anh' => 'Có ảnh',
            'co_ban_cong' => 'Có ban công',
            'co_bep_rieng' => 'Có bếp riêng',
            'co_huong_bien' => 'Hướng biển',
        ];
    }

    private function laLoiThieuBang(QueryException $exception): bool
    {
        $thongBao = strtolower($exception->getMessage());

        return str_contains($thongBao, 'no such table') || str_contains($thongBao, 'base table or view not found');
    }
}
