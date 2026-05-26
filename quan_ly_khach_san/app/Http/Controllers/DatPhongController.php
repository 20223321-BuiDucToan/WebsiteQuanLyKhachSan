<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDatPhong;
use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Support\DatPhongKhachDatQuaHan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatPhongController extends Controller
{
    private const TRANG_THAI_XUNG_DOT = [
        'cho_xac_nhan',
        'da_xac_nhan',
        'da_nhan_phong',
    ];

    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in(DatPhong::DANH_SACH_TRANG_THAI)],
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
            'san_sang_hom_nay' => $danhSachPhong->filter(fn(Phong $phong) => $phong->trang_thai === Phong::TRANG_THAI_TRONG)->count(),
            'dang_su_dung' => $danhSachPhong->where('trang_thai', Phong::TRANG_THAI_DANG_SU_DUNG)->count(),
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
        $daTaoHoaDonSom = false;

        if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_DA_NHAN_PHONG && ! $ngayNhan->isSameDay(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Chi co the tao don o trang thai da nhan phong khi ngay nhan phong la hom nay.',
            ]);
        }

        $datPhong = DB::transaction(function () use ($duLieu, $phong, $giaMotDem, $ngayNhan, $ngayTra, $soDem, &$daTaoHoaDonSom) {
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

            if (in_array($duLieu['trang_thai'], [DatPhong::TRANG_THAI_DA_XAC_NHAN, DatPhong::TRANG_THAI_DA_NHAN_PHONG], true)) {
                $daTaoHoaDonSom = $this->taoHoacDongBoHoaDonTheoNghiepVu(
                    $datPhong->fresh(['chiTietDatPhong', 'suDungDichVu', 'hoaDon.thanhToan']),
                    'Hóa đơn được tạo sớm để theo dõi tiền cọc giữ phòng trước ngày nhận phòng.'
                );
            }

            return $datPhong;
        });

        $thongBao = 'Tạo đơn đặt phòng thành công.';

        if ($daTaoHoaDonSom) {
            $thongBao .= ' Hệ thống đã tạo luôn hóa đơn để theo dõi đặt cọc.';
        }

        return redirect()
            ->route('dat-phong.show', $datPhong)
            ->with('success', $thongBao);
    }

    public function show(DatPhong $datPhong)
    {
        $datPhong->load([
            'khachHang',
            'nguoiTao',
            'chiTietDatPhong.phong.loaiPhong',
            'suDungDichVu.dichVu',
            'suDungDichVu.nguoiTao',
            'hoaDon.thanhToan',
        ]);

        $datPhong = $this->boSungDuLieuDatPhong($datPhong);
        $hoaDonHienTai = $datPhong->hoa_don_hien_tai;
        $timeline = $this->taoTimelineDatPhong($datPhong, $hoaDonHienTai);
        $danhSachDichVuHoatDong = \App\Models\DichVu::query()
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('ten_dich_vu')
            ->get();
        $coTheCapNhatDichVu = in_array($datPhong->trang_thai, ['da_xac_nhan', 'da_nhan_phong', 'da_tra_phong'], true)
            && (!$hoaDonHienTai || $hoaDonHienTai->trang_thai !== 'da_thanh_toan');

        return view('dat_phong.show', [
            'datPhong' => $datPhong,
            'tongTienPhong' => $datPhong->tong_tien_tam_tinh,
            'tongTienDichVu' => $datPhong->tong_tien_dich_vu,
            'tongThanhToanDuKien' => $datPhong->tong_thanh_toan_du_kien,
            'hoaDonHienTai' => $hoaDonHienTai,
            'timeline' => $timeline,
            'danhSachDichVuHoatDong' => $danhSachDichVuHoatDong,
            'coTheCapNhatDichVu' => $coTheCapNhatDichVu,
        ]);
    }

    public function capNhatTrangThai(Request $request, DatPhong $datPhong)
    {
        $duLieu = $request->validate([
            'trang_thai' => [
                'required',
                Rule::in(DatPhong::DANH_SACH_TRANG_THAI),
            ],
            'phi_khong_den' => ['nullable', 'numeric', 'min:0'],
            'ly_do_khong_den' => ['nullable', 'string', 'max:1000'],
        ]);

        $trangThaiHienTai = (string) $datPhong->trang_thai;
        $trangThaiMoi = (string) $duLieu['trang_thai'];

        if ($trangThaiMoi === $trangThaiHienTai) {
            return redirect()
                ->back()
                ->with('success', 'Trang thai don dat phong duoc giu nguyen.');
        }

        if (!in_array($trangThaiMoi, DatPhong::layTrangThaiKeTiepHopLe($trangThaiHienTai), true)) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Chi duoc chuyen don theo dung thu tu nghiep vu.',
            ]);
        }

        $daTaoHoaDonTuDong = false;
        $daHuyHoaDonLienQuan = false;
        $daDongBoHoaDonNoShow = false;

        DB::transaction(function () use ($datPhong, $duLieu, &$daTaoHoaDonTuDong, &$daHuyHoaDonLienQuan, &$daDongBoHoaDonNoShow) {
            if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_DA_NHAN_PHONG) {
                $this->baoDamCoTheNhanPhong($datPhong);
            }

            if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_KHONG_DEN) {
                $this->baoDamCoTheDanhDauKhongDen($datPhong);
            }

            if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_DA_HUY) {
                $daHuyHoaDonLienQuan = $this->dongBoHoaDonKhiHuyDatPhong($datPhong);
            }

            $duLieuCapNhatDatPhong = [
                'trang_thai' => $duLieu['trang_thai'],
            ];

            if ($duLieu['trang_thai'] === 'da_nhan_phong' && !$datPhong->ngay_nhan_phong_thuc_te) {
                $duLieuCapNhatDatPhong['ngay_nhan_phong_thuc_te'] = now();
            }

            if ($duLieu['trang_thai'] === 'da_tra_phong' && !$datPhong->ngay_tra_phong_thuc_te) {
                $duLieuCapNhatDatPhong['ngay_tra_phong_thuc_te'] = now();
            }

            if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_KHONG_DEN) {
                $hoaDonLienQuan = $datPhong->hoaDon()
                    ->with('thanhToan')
                    ->where('trang_thai', '!=', 'da_huy')
                    ->latest('id')
                    ->first();

                $soTienDaThu = $hoaDonLienQuan ? $hoaDonLienQuan->tinhTongTienDaThu() : 0;

                $duLieuCapNhatDatPhong['thoi_diem_khong_den'] = $datPhong->thoi_diem_khong_den ?? now();
                $duLieuCapNhatDatPhong['phi_khong_den'] = $this->xacDinhPhiKhongDen(
                    $datPhong,
                    array_key_exists('phi_khong_den', $duLieu) ? (float) $duLieu['phi_khong_den'] : null,
                    $soTienDaThu
                );
                $duLieuCapNhatDatPhong['ly_do_khong_den'] = $duLieu['ly_do_khong_den'] ?? $datPhong->ly_do_khong_den;
            }

            $datPhong->update($duLieuCapNhatDatPhong);

            $trangThaiChiTiet = $this->mapTrangThaiChiTiet($duLieu['trang_thai']);

            if ($trangThaiChiTiet) {
                $duLieuCapNhatChiTiet = [
                    'trang_thai' => $trangThaiChiTiet,
                ];

                if ($duLieu['trang_thai'] === 'da_nhan_phong') {
                    $duLieuCapNhatChiTiet['ngay_nhan_phong_thuc_te'] = now();
                }

                if ($duLieu['trang_thai'] === 'da_tra_phong') {
                    $duLieuCapNhatChiTiet['ngay_tra_phong_thuc_te'] = now();
                }

                $datPhong->chiTietDatPhong()->update($duLieuCapNhatChiTiet);
            }

            $datPhongSauCapNhat = $datPhong->fresh(['chiTietDatPhong.phong', 'chiTietDatPhong', 'hoaDon']);

            $this->dongBoTrangThaiPhongTheoDatPhong($datPhongSauCapNhat);

            if (in_array($duLieu['trang_thai'], [
                DatPhong::TRANG_THAI_DA_XAC_NHAN,
                DatPhong::TRANG_THAI_DA_NHAN_PHONG,
                DatPhong::TRANG_THAI_KHONG_DEN,
                DatPhong::TRANG_THAI_DA_TRA_PHONG,
            ], true)) {
                $ghiChuTuDong = match ($duLieu['trang_thai']) {
                    DatPhong::TRANG_THAI_DA_XAC_NHAN => 'Hóa đơn được tạo sớm để theo dõi tiền cọc giữ phòng trước ngày nhận phòng.',
                    DatPhong::TRANG_THAI_KHONG_DEN => 'Đơn đặt phòng đã được đánh dấu khách không đến. Hệ thống ghi nhận phí no-show để tiếp tục theo dõi công nợ.',
                    DatPhong::TRANG_THAI_DA_TRA_PHONG => 'Hóa đơn được tạo tự động khi đơn đặt phòng chuyển sang trạng thái đã trả phòng.',
                    default => null,
                };

                $daTaoHoaDonTuDong = $this->taoHoacDongBoHoaDonTheoNghiepVu(
                    $datPhongSauCapNhat->fresh(['chiTietDatPhong', 'suDungDichVu', 'hoaDon.thanhToan']),
                    $ghiChuTuDong
                ) || $daTaoHoaDonTuDong;
            }

            if ($duLieu['trang_thai'] === DatPhong::TRANG_THAI_KHONG_DEN) {
                $daDongBoHoaDonNoShow = true;
            }
        });

        $thongBao = 'Cap nhat trang thai don dat phong thanh cong.';

        if ($daHuyHoaDonLienQuan) {
            $thongBao .= ' He thong da huy hoa don chua thu tien lien quan.';
        }

        if ($daTaoHoaDonTuDong) {
            $thongBao .= ' He thong da tu dong tao hoa don.';
        }

        if ($daDongBoHoaDonNoShow) {
            $thongBao .= ' He thong da cap nhat phi no-show va giai phong phong cho lich ban tiep theo.';
        }

        return redirect()
            ->back()
            ->with('success', $thongBao);
    }

    private function taoTruyVanDatPhong(?string $tuKhoa, ?string $trangThai, ?string $nguonDat, ?string $tuNgay, ?string $denNgay)
    {
        return DatPhong::query()
            ->with(['khachHang', 'chiTietDatPhong.phong.loaiPhong', 'hoaDon' => function ($query) {
                $query->with('thanhToan')->where('trang_thai', '!=', 'da_huy')->latest('id');
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
        $tongTienPhongGoc = $datPhong->tinhTongTienPhong();
        $tongTienTamTinh = $datPhong->tinhTongTienPhongTheoNghiepVu();
        $tongTienDichVu = $datPhong->tinhTongTienDichVuTheoNghiepVu();

        $tongSoPhong = $datPhong->chiTietDatPhong->count();
        $tongSoDem = (int) $datPhong->chiTietDatPhong->sum('so_dem');
        $hoaDonHienTai = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();

        if ($hoaDonHienTai) {
            $hoaDonHienTai->dongBoGiaTriTuDatPhong(false);
        }

        $soTienDaThuHoaDon = $hoaDonHienTai && $hoaDonHienTai->relationLoaded('thanhToan')
            ? (float) $hoaDonHienTai->thanhToan->where('trang_thai', 'thanh_cong')->sum('so_tien')
            : 0;
        $soTienChoXuLyHoaDon = $hoaDonHienTai && $hoaDonHienTai->relationLoaded('thanhToan')
            ? (float) $hoaDonHienTai->thanhToan->where('trang_thai', 'cho_xu_ly')->sum('so_tien')
            : 0;
        $soTienConLaiHoaDon = $hoaDonHienTai
            ? max(0, (float) $hoaDonHienTai->tong_tien - $soTienDaThuHoaDon)
            : 0;
        $tienCocGoiY = $datPhong->tinhTienCocGoiY();
        $hanThanhToanDatCoc = $this->tinhHanThanhToanDatCocGoiY($datPhong);
        $phiKhongDen = $datPhong->trang_thai === DatPhong::TRANG_THAI_KHONG_DEN
            ? $datPhong->tinhTongTienPhongTheoNghiepVu($soTienDaThuHoaDon)
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
        $khachKhongDen = $datPhong->trang_thai === DatPhong::TRANG_THAI_KHONG_DEN;

        $mucDoUuTien = 'thap';
        $ghiChuVanHanh = 'Đơn đang ở trạng thái theo dõi bình thường.';

        if ($khachKhongDen) {
            $mucDoUuTien = 'trung_binh';
            $ghiChuVanHanh = 'Khách không đến theo lịch xác nhận. Cần chốt phí no-show và tiếp tục theo dõi công nợ nếu còn.';
        } elseif ($quaHanTraPhong) {
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
        $datPhong->setAttribute('tong_tien_phong_goc', $tongTienPhongGoc);
        $datPhong->setAttribute('tong_tien_dich_vu', $tongTienDichVu);
        $datPhong->setAttribute('tong_thanh_toan_du_kien', max(0, $tongTienTamTinh + $tongTienDichVu));
        $datPhong->setAttribute('tong_so_phong', $tongSoPhong);
        $datPhong->setAttribute('tong_so_dem', $tongSoDem);
        $datPhong->setAttribute('hoa_don_hien_tai', $hoaDonHienTai);
        $datPhong->setAttribute('so_tien_da_thu_hoa_don', $soTienDaThuHoaDon);
        $datPhong->setAttribute('so_tien_cho_xu_ly_hoa_don', $soTienChoXuLyHoaDon);
        $datPhong->setAttribute('so_tien_con_lai_hoa_don', $soTienConLaiHoaDon);
        $datPhong->setAttribute('tong_tien_dat_coc_goi_y', $tienCocGoiY);
        $datPhong->setAttribute('han_thanh_toan_dat_coc', $hanThanhToanDatCoc);
        $datPhong->setAttribute('so_tien_con_thieu_dat_coc', max(0, $tienCocGoiY - $soTienDaThuHoaDon));
        $datPhong->setAttribute('so_tien_con_thieu_dat_coc_sau_cho_xu_ly', max(0, $tienCocGoiY - $soTienDaThuHoaDon - $soTienChoXuLyHoaDon));
        $datPhong->setAttribute('phi_khong_den_hien_tai', $phiKhongDen);
        $datPhong->setAttribute('can_xu_ly_ngay', in_array($mucDoUuTien, ['cao', 'trung_binh'], true));
        $datPhong->setAttribute('muc_do_uu_tien', $mucDoUuTien);
        $datPhong->setAttribute('ghi_chu_van_hanh', $ghiChuVanHanh);
        $datPhong->setAttribute('can_xac_nhan', $canXacNhan);
        $datPhong->setAttribute('nhan_phong_hom_nay', $nhanPhongHomNay);
        $datPhong->setAttribute('sap_tra_phong', $sapTraPhong);
        $datPhong->setAttribute('qua_han_tra_phong', $quaHanTraPhong);
        $datPhong->setAttribute('khach_khong_den', $khachKhongDen);

        return $this->boSungThongTinTuDongXuLyKhachDat($datPhong);
    }

    private function tongHopDatPhong(Collection $danhSachDatPhong): array
    {
        return [
            'tong_don' => $danhSachDatPhong->count(),
            'tong_doanh_thu_tam_tinh' => (float) $danhSachDatPhong->sum('tong_tien_tam_tinh'),
            'cho_xac_nhan' => $danhSachDatPhong->where('trang_thai', 'cho_xac_nhan')->count(),
            'da_xac_nhan' => $danhSachDatPhong->where('trang_thai', 'da_xac_nhan')->count(),
            'dang_luu_tru' => $danhSachDatPhong->where('trang_thai', 'da_nhan_phong')->count(),
            'khong_den' => $danhSachDatPhong->where('trang_thai', DatPhong::TRANG_THAI_KHONG_DEN)->count(),
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

        if ($datPhong->trang_thai === DatPhong::TRANG_THAI_KHONG_DEN) {
            $timeline[] = [
                'label' => 'Khách không đến',
                'thoi_gian' => $datPhong->thoi_diem_khong_den ?? $datPhong->ngay_nhan_phong_du_kien,
                'ghi_chu' => 'Đơn được chuyển sang nghiệp vụ no-show. Phí phát sinh sẽ được theo dõi qua hóa đơn liên quan.',
                'class' => 'chip chip-danger',
                'co_gio' => (bool) $datPhong->thoi_diem_khong_den,
            ];
        }

        return $timeline;
    }

    private function boSungThongTinTuDongXuLyKhachDat(DatPhong $datPhong): DatPhong
    {
        foreach (DatPhongKhachDatQuaHan::taoChiSoHienThi($datPhong) as $thuocTinh => $giaTri) {
            $datPhong->setAttribute($thuocTinh, $giaTri);
        }

        return $datPhong;
    }

    private function mapTrangThaiChiTiet(string $trangThaiDatPhong): ?string
    {
        return match ($trangThaiDatPhong) {
            'cho_xac_nhan', 'da_xac_nhan' => 'da_dat',
            'da_nhan_phong' => 'dang_o',
            DatPhong::TRANG_THAI_KHONG_DEN => DatPhong::TRANG_THAI_KHONG_DEN,
            'da_tra_phong' => 'da_tra_phong',
            'da_huy' => 'da_huy',
            default => null,
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
        foreach ($datPhong->chiTietDatPhong as $chiTiet) {
            if (!$chiTiet->phong) {
                continue;
            }

            $duLieuCapNhatPhong = [];

            if ($datPhong->trang_thai === 'da_nhan_phong') {
                $duLieuCapNhatPhong['tinh_trang_ve_sinh'] = 'sach';
            } elseif ($datPhong->trang_thai === 'da_tra_phong' || ($datPhong->trang_thai === 'da_huy' && $datPhong->ngay_nhan_phong_thuc_te)) {
                $duLieuCapNhatPhong['tinh_trang_ve_sinh'] = 'can_don';
            }

            if ($duLieuCapNhatPhong !== []) {
                $chiTiet->phong->forceFill($duLieuCapNhatPhong)->saveQuietly();
            }

            $chiTiet->phong->refresh()->dongBoTrangThaiHeThong();
        }
    }

    private function taoMaDatPhong(): string
    {
        do {
            $maDatPhong = 'DP' . now()->format('ymdHis') . random_int(10, 99);
        } while (DatPhong::query()->where('ma_dat_phong', $maDatPhong)->exists());

        return $maDatPhong;
    }

    private function baoDamCoTheNhanPhong(DatPhong $datPhong): void
    {
        $ngayNhanDuKien = $datPhong->ngay_nhan_phong_du_kien?->copy()->startOfDay();

        if (! $ngayNhanDuKien || $ngayNhanDuKien->gt(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Khong the nhan phong truoc ngay nhan phong du kien. Hay dieu chinh lich luu tru truoc.',
            ]);
        }
    }

    private function baoDamCoTheDanhDauKhongDen(DatPhong $datPhong): void
    {
        $ngayNhanDuKien = $datPhong->ngay_nhan_phong_du_kien?->copy()->startOfDay();

        if (! $ngayNhanDuKien || $ngayNhanDuKien->gt(now()->startOfDay())) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Chi co the danh dau khach khong den khi da toi ngay nhan phong du kien.',
            ]);
        }
    }

    private function xacDinhPhiKhongDen(DatPhong $datPhong, ?float $phiKhongDenNhapTay, float $soTienDaThu): float
    {
        $tongTienPhong = $datPhong->tinhTongTienPhong();
        $phiMacDinh = $datPhong->tinhPhiKhongDenMacDinh($soTienDaThu);

        if ($phiKhongDenNhapTay === null) {
            return $phiMacDinh;
        }

        if ($phiKhongDenNhapTay > $tongTienPhong) {
            throw ValidationException::withMessages([
                'phi_khong_den' => 'Phi no-show khong duoc vuot qua tong tien phong cua don dat.',
            ]);
        }

        if ($phiKhongDenNhapTay < $soTienDaThu) {
            throw ValidationException::withMessages([
                'phi_khong_den' => 'Phi no-show khong duoc nho hon so tien da thu thanh cong vi he thong chua ho tro hoan tien tu dong.',
            ]);
        }

        return $phiKhongDenNhapTay;
    }

    private function tinhHanThanhToanDatCocGoiY(DatPhong $datPhong): ?Carbon
    {
        if (! $datPhong->ngay_nhan_phong_du_kien) {
            return null;
        }

        $hanThanhToan = $datPhong->ngay_nhan_phong_du_kien
            ->copy()
            ->subDay()
            ->setTime(18, 0);

        if ($hanThanhToan->lt(now())) {
            return now()->copy()->addHour();
        }

        return $hanThanhToan;
    }

    private function taoHoacDongBoHoaDonTheoNghiepVu(DatPhong $datPhong, ?string $ghiChuMacDinh = null): bool
    {
        $datPhong->loadMissing(['hoaDon.thanhToan', 'chiTietDatPhong', 'suDungDichVu']);

        $hoaDonDangHoatDong = $datPhong->hoaDon
            ->where('trang_thai', '!=', 'da_huy')
            ->sortByDesc('id')
            ->first();

        if ($hoaDonDangHoatDong) {
            $hoaDonDangHoatDong->dongBoGiaTriTuDatPhong();
            $this->boSungGhiChuNghiepVuHoaDon($hoaDonDangHoatDong, $ghiChuMacDinh);

            return false;
        }

        $tongTienPhong = $datPhong->tinhTongTienPhongTheoNghiepVu();
        $tongTienDichVu = $datPhong->tinhTongTienDichVuTheoNghiepVu();
        $tongTien = max(0, $tongTienPhong + $tongTienDichVu);
        $trangThaiHoaDon = $tongTien > 0 ? 'chua_thanh_toan' : 'da_thanh_toan';

        $hoaDon = HoaDon::query()->create([
            'ma_hoa_don' => $this->taoMaHoaDon(),
            'dat_phong_id' => $datPhong->id,
            'tong_tien_phong' => $tongTienPhong,
            'tong_tien_dich_vu' => $tongTienDichVu,
            'giam_gia' => 0,
            'thue' => 0,
            'tong_tien' => $tongTien,
            'trang_thai' => $trangThaiHoaDon,
            'thoi_diem_xuat' => now(),
            'nguoi_tao_id' => auth()->id(),
            'ghi_chu' => $ghiChuMacDinh,
        ]);

        $hoaDon->giaiPhongSauKhiHoanTatThanhToan();

        return true;
    }

    private function boSungGhiChuNghiepVuHoaDon(HoaDon $hoaDon, ?string $ghiChuMacDinh): void
    {
        if (blank($ghiChuMacDinh)) {
            return;
        }

        $ghiChuHienTai = trim((string) $hoaDon->ghi_chu);

        if (str_contains($ghiChuHienTai, $ghiChuMacDinh)) {
            return;
        }

        $hoaDon->forceFill([
            'ghi_chu' => $ghiChuHienTai === ''
                ? $ghiChuMacDinh
                : $ghiChuHienTai . PHP_EOL . $ghiChuMacDinh,
        ])->saveQuietly();
    }

    private function dongBoHoaDonKhiHuyDatPhong(DatPhong $datPhong): bool
    {
        $danhSachHoaDonDangHoatDong = $datPhong->hoaDon()
            ->with('thanhToan')
            ->where('trang_thai', '!=', DatPhong::TRANG_THAI_DA_HUY)
            ->get();

        if ($danhSachHoaDonDangHoatDong->isEmpty()) {
            return false;
        }

        if ($danhSachHoaDonDangHoatDong->contains(fn(HoaDon $hoaDon) => $hoaDon->coThanhToanThanhCong())) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Khong the huy don dat phong khi hoa don lien quan da ghi nhan thanh toan thanh cong.',
            ]);
        }

        foreach ($danhSachHoaDonDangHoatDong as $hoaDon) {
            $ghiChuHienTai = trim((string) $hoaDon->ghi_chu);
            $ghiChuTuDong = 'Hoa don duoc huy tu dong khi don dat phong chuyen sang da huy.';

            $hoaDon->forceFill([
                'trang_thai' => DatPhong::TRANG_THAI_DA_HUY,
                'ghi_chu' => $ghiChuHienTai === ''
                    ? $ghiChuTuDong
                    : $ghiChuHienTai . PHP_EOL . $ghiChuTuDong,
            ])->saveQuietly();
        }

        return true;
    }

    private function taoHoaDonTuDongNeuCan(DatPhong $datPhong): bool
    {
        return $this->taoHoacDongBoHoaDonTheoNghiepVu(
            $datPhong,
            'Hóa đơn được tạo tự động khi đơn đặt phòng chuyển sang trạng thái đã trả phòng.'
        );
    }

    private function taoMaHoaDon(): string
    {
        do {
            $maHoaDon = 'HD' . now()->format('ymdHis') . random_int(10, 99);
        } while (HoaDon::query()->where('ma_hoa_don', $maHoaDon)->exists());

        return $maHoaDon;
    }
}
