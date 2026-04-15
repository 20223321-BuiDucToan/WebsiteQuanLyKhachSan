<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(self::TRANG_THAI_DAT_PHONG)],
            'nguon_dat' => ['nullable', Rule::in(['truc_tiep', 'website', 'dien_thoai', 'zalo', 'khac'])],
            'tu_ngay' => ['nullable', 'date'],
            'den_ngay' => ['nullable', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $nguonDat = $request->input('nguon_dat');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');

        $truyVanDatPhong = $this->taoTruyVanDatPhong($tuKhoa, $trangThai, $nguonDat, $tuNgay, $denNgay);

        $danhSachDatPhong = (clone $truyVanDatPhong)
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $danhSachDatPhong->setCollection(
            $danhSachDatPhong->getCollection()->map(fn(DatPhong $datPhong) => $this->boSungDuLieuDatPhong($datPhong))
        );

        $danhSachDatPhongDaLoc = (clone $truyVanDatPhong)
            ->get()
            ->map(fn(DatPhong $datPhong) => $this->boSungDuLieuDatPhong($datPhong));

        $thongKe = $this->tongHopDatPhong($danhSachDatPhongDaLoc);
        $datPhongCanChuY = $danhSachDatPhongDaLoc
            ->filter(fn(DatPhong $datPhong) => $datPhong->can_xu_ly_ngay)
            ->sortByDesc(fn(DatPhong $datPhong) => ($datPhong->muc_do_uu_tien === 'cao' ? 1000000000000 : 0) + $datPhong->tong_tien_tam_tinh)
            ->take(5)
            ->values();

        return view('dat_phong.index', compact(
            'danhSachDatPhong',
            'tuKhoa',
            'trangThai',
            'nguonDat',
            'tuNgay',
            'denNgay',
            'thongKe',
            'datPhongCanChuY'
        ));
    }

    public function create()
    {
        $danhSachPhong = Phong::query()
            ->with('loaiPhong')
            ->where('tinh_trang_hoat_dong', 'hoat_dong')
            ->orderBy('so_phong')
            ->get();

        $giaTheoPhong = $danhSachPhong->map(function (Phong $phong) {
            return (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong?->gia_mot_dem ?? 0);
        });

        $thongKePhong = [
            'tong_hoat_dong' => $danhSachPhong->count(),
            'san_sang_hom_nay' => $danhSachPhong->filter(fn(Phong $phong) => in_array($phong->trang_thai, ['trong', 'don_dep'], true))->count(),
            'dang_su_dung' => $danhSachPhong->where('trang_thai', 'dang_su_dung')->count(),
            'gia_trung_binh' => $giaTheoPhong->filter(fn(float $gia) => $gia > 0)->avg() ?? 0,
        ];

        return view('dat_phong.create', [
            'danhSachPhong' => $danhSachPhong,
            'thongKePhong' => $thongKePhong,
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
            'hoaDon.thanhToan',
        ]);

        $datPhong = $this->boSungDuLieuDatPhong($datPhong);
        $hoaDonHienTai = $datPhong->hoa_don_hien_tai;
        $timeline = $this->taoTimelineDatPhong($datPhong, $hoaDonHienTai);

        return view('dat_phong.show', [
            'datPhong' => $datPhong,
            'tongTienPhong' => $datPhong->tong_tien_tam_tinh,
            'hoaDonHienTai' => $hoaDonHienTai,
            'timeline' => $timeline,
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

    private function taoTruyVanDatPhong(?string $tuKhoa, ?string $trangThai, ?string $nguonDat, ?string $tuNgay, ?string $denNgay)
    {
        return DatPhong::query()
            ->with(['khachHang', 'chiTietDatPhong.phong.loaiPhong', 'hoaDon' => function ($query) {
                $query->where('trang_thai', '!=', 'da_huy')->latest('id');
            }])
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
            });
    }

    private function boSungDuLieuDatPhong(DatPhong $datPhong): DatPhong
    {
        $tongTienTamTinh = (float) $datPhong->chiTietDatPhong->sum(function (ChiTietDatPhong $chiTiet) {
            return (float) $chiTiet->gia_phong * (int) $chiTiet->so_dem;
        });

        $tongSoPhong = $datPhong->chiTietDatPhong->count();
        $tongSoDem = (int) $datPhong->chiTietDatPhong->sum('so_dem');
        $hoaDonHienTai = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();

        $soTienDaThuHoaDon = $hoaDonHienTai && $hoaDonHienTai->relationLoaded('thanhToan')
            ? (float) $hoaDonHienTai->thanhToan->where('trang_thai', 'thanh_cong')->sum('so_tien')
            : 0;
        $soTienConLaiHoaDon = $hoaDonHienTai
            ? max(0, (float) $hoaDonHienTai->tong_tien - $soTienDaThuHoaDon)
            : 0;

        $homNay = now()->startOfDay();
        $ngayNhanPhong = $datPhong->ngay_nhan_phong_du_kien?->copy()->startOfDay();
        $ngayTraPhong = $datPhong->ngay_tra_phong_du_kien?->copy()->startOfDay();

        $canXacNhan = $datPhong->trang_thai === 'cho_xac_nhan';
        $nhanPhongHomNay = in_array($datPhong->trang_thai, ['cho_xac_nhan', 'da_xac_nhan'], true)
            && $ngayNhanPhong
            && $ngayNhanPhong->equalTo($homNay);
        $nhanPhongSom = in_array($datPhong->trang_thai, ['cho_xac_nhan', 'da_xac_nhan'], true)
            && $ngayNhanPhong
            && $ngayNhanPhong->greaterThan($homNay)
            && $ngayNhanPhong->lessThanOrEqualTo($homNay->copy()->addDay());
        $quaHanTraPhong = $datPhong->trang_thai === 'da_nhan_phong'
            && $ngayTraPhong
            && $ngayTraPhong->lt($homNay);
        $sapTraPhong = $datPhong->trang_thai === 'da_nhan_phong'
            && $ngayTraPhong
            && $ngayTraPhong->greaterThanOrEqualTo($homNay)
            && $ngayTraPhong->lessThanOrEqualTo($homNay->copy()->addDay());

        $mucDoUuTien = 'thap';
        $ghiChuVanHanh = 'Đơn đang ở trạng thái theo dõi bình thường.';

        if ($quaHanTraPhong) {
            $mucDoUuTien = 'cao';
            $ghiChuVanHanh = 'Khách đang quá hạn trả phòng, cần xử lý ngay.';
        } elseif ($canXacNhan) {
            $mucDoUuTien = 'cao';
            $ghiChuVanHanh = 'Đơn mới chờ xác nhận từ bộ phận vận hành.';
        } elseif ($nhanPhongHomNay) {
            $mucDoUuTien = 'cao';
            $ghiChuVanHanh = 'Khách dự kiến nhận phòng hôm nay.';
        } elseif ($sapTraPhong) {
            $mucDoUuTien = 'trung_binh';
            $ghiChuVanHanh = 'Đơn sắp đến thời điểm trả phòng.';
        } elseif ($nhanPhongSom) {
            $mucDoUuTien = 'trung_binh';
            $ghiChuVanHanh = 'Đơn có lịch nhận phòng trong 24 giờ tới.';
        }

        $datPhong->setAttribute('tong_tien_tam_tinh', $tongTienTamTinh);
        $datPhong->setAttribute('tong_so_phong', $tongSoPhong);
        $datPhong->setAttribute('tong_so_dem', $tongSoDem);
        $datPhong->setAttribute('hoa_don_hien_tai', $hoaDonHienTai);
        $datPhong->setAttribute('so_tien_da_thu_hoa_don', $soTienDaThuHoaDon);
        $datPhong->setAttribute('so_tien_con_lai_hoa_don', $soTienConLaiHoaDon);
        $datPhong->setAttribute('can_xu_ly_ngay', in_array($mucDoUuTien, ['cao', 'trung_binh'], true));
        $datPhong->setAttribute('muc_do_uu_tien', $mucDoUuTien);
        $datPhong->setAttribute('ghi_chu_van_hanh', $ghiChuVanHanh);
        $datPhong->setAttribute('can_xac_nhan', $canXacNhan);
        $datPhong->setAttribute('nhan_phong_hom_nay', $nhanPhongHomNay);
        $datPhong->setAttribute('sap_tra_phong', $sapTraPhong);
        $datPhong->setAttribute('qua_han_tra_phong', $quaHanTraPhong);

        return $datPhong;
    }

    private function tongHopDatPhong(Collection $danhSachDatPhong): array
    {
        return [
            'tong_don' => $danhSachDatPhong->count(),
            'tong_doanh_thu_tam_tinh' => (float) $danhSachDatPhong->sum('tong_tien_tam_tinh'),
            'cho_xac_nhan' => $danhSachDatPhong->where('trang_thai', 'cho_xac_nhan')->count(),
            'da_xac_nhan' => $danhSachDatPhong->where('trang_thai', 'da_xac_nhan')->count(),
            'dang_luu_tru' => $danhSachDatPhong->where('trang_thai', 'da_nhan_phong')->count(),
            'da_tra_phong' => $danhSachDatPhong->where('trang_thai', 'da_tra_phong')->count(),
            'website' => $danhSachDatPhong->where('nguon_dat', 'website')->count(),
            'co_hoa_don' => $danhSachDatPhong->filter(fn(DatPhong $datPhong) => (bool) $datPhong->hoa_don_hien_tai)->count(),
            'can_xu_ly_ngay' => $danhSachDatPhong->where('can_xu_ly_ngay', true)->count(),
            'nhan_phong_hom_nay' => $danhSachDatPhong->where('nhan_phong_hom_nay', true)->count(),
            'sap_tra_phong' => $danhSachDatPhong->where('sap_tra_phong', true)->count(),
        ];
    }

    private function taoTimelineDatPhong(DatPhong $datPhong, ?HoaDon $hoaDonHienTai): array
    {
        $timeline = [
            [
                'label' => 'Tạo đơn đặt phòng',
                'thoi_gian' => $datPhong->ngay_dat,
                'ghi_chu' => 'Đơn được tạo và ghi nhận trên hệ thống.',
                'class' => 'chip chip-info',
                'co_gio' => true,
            ],
            [
                'label' => 'Nhận phòng',
                'thoi_gian' => $datPhong->ngay_nhan_phong_thuc_te ?? $datPhong->ngay_nhan_phong_du_kien,
                'ghi_chu' => $datPhong->ngay_nhan_phong_thuc_te
                    ? 'Đã nhận phòng thực tế.'
                    : 'Lịch nhận phòng dự kiến.',
                'class' => $datPhong->ngay_nhan_phong_thuc_te ? 'chip chip-success' : 'chip chip-warning',
                'co_gio' => (bool) $datPhong->ngay_nhan_phong_thuc_te,
            ],
            [
                'label' => 'Trả phòng',
                'thoi_gian' => $datPhong->ngay_tra_phong_thuc_te ?? $datPhong->ngay_tra_phong_du_kien,
                'ghi_chu' => $datPhong->ngay_tra_phong_thuc_te
                    ? 'Khách đã hoàn tất trả phòng.'
                    : 'Mốc trả phòng dự kiến.',
                'class' => $datPhong->ngay_tra_phong_thuc_te ? 'chip chip-success' : 'chip chip-neutral',
                'co_gio' => (bool) $datPhong->ngay_tra_phong_thuc_te,
            ],
        ];

        if ($hoaDonHienTai) {
            $timeline[] = [
                'label' => 'Hóa đơn liên quan',
                'thoi_gian' => $hoaDonHienTai->thoi_diem_xuat,
                'ghi_chu' => 'Đã phát sinh hóa đơn ' . $hoaDonHienTai->ma_hoa_don . '.',
                'class' => 'chip chip-info',
                'co_gio' => true,
            ];
        }

        return $timeline;
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
