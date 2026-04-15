<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class KhachHangController extends Controller
{
    public function index(Request $request)
    {
        $this->dongBoKhachHangTuTaiKhoan();

        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'hang_khach_hang' => ['nullable', Rule::in(['thuong', 'bac', 'vang', 'kim_cuong'])],
            'trang_thai' => ['nullable', Rule::in(['hoat_dong', 'tam_khoa'])],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $hangKhachHang = $request->input('hang_khach_hang');
        $trangThai = $request->input('trang_thai');

        $khachHangQuery = $this->taoTruyVanKhachHang($tuKhoa, $hangKhachHang, $trangThai);

        $danhSachKhachHang = (clone $khachHangQuery)
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $danhSachKhachHang->setCollection(
            $danhSachKhachHang->getCollection()->map(fn(KhachHang $khachHang) => $this->boSungDuLieuKhachHang($khachHang))
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

        return view('khach_hang.index', compact(
            'danhSachKhachHang',
            'tuKhoa',
            'hangKhachHang',
            'trangThai',
            'thongKe',
            'khachHangNoiBat'
        ));
    }

    public function show(KhachHang $khachHang)
    {
        $khachHang->load([
            'datPhong' => function ($query) {
                $query->with('chiTietDatPhong.phong')->latest('id');
            },
        ]);

        return view('khach_hang.show', [
            'khachHang' => $khachHang,
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
        $duLieu = $request->validate([
            'ho_ten' => ['required', 'string', 'max:100'],
            'gioi_tinh' => ['nullable', Rule::in(['nam', 'nu', 'khac'])],
            'ngay_sinh' => ['nullable', 'date', 'before:today'],
            'so_dien_thoai' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'so_giay_to' => ['nullable', 'string', 'max:50'],
            'loai_giay_to' => ['nullable', Rule::in(['cccd', 'cmnd', 'passport', 'khac'])],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'quoc_tich' => ['nullable', 'string', 'max:50'],
            'hang_khach_hang' => ['required', Rule::in(['thuong', 'bac', 'vang', 'kim_cuong'])],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_khoa'])],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);

        $khachHang->update($duLieu);

        return redirect()
            ->route('khach-hang.show', $khachHang)
            ->with('success', 'Cập nhật khách hàng thành công.');
    }

    public function doiTrangThai(KhachHang $khachHang)
    {
        $khachHang->update([
            'trang_thai' => $khachHang->trang_thai === 'hoat_dong' ? 'tam_khoa' : 'hoat_dong',
        ]);

        return redirect()
            ->route('khach-hang.index')
            ->with('success', 'Đã đổi trạng thái khách hàng.');
    }

    private function taoTruyVanKhachHang(?string $tuKhoa, ?string $hangKhachHang, ?string $trangThai)
    {
        return KhachHang::query()
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
            ->when($hangKhachHang, function ($query) use ($hangKhachHang) {
                $query->where('hang_khach_hang', $hangKhachHang);
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            });
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

        $nhomKhachHang = 'moi';

        if ($khachHang->hang_khach_hang === 'kim_cuong' || $tongDoanhThu >= 20000000 || $khachHang->dat_phong_count >= 5) {
            $nhomKhachHang = 'trung_thanh';
        } elseif ($khachHang->dat_phong_count >= 2 || $khachHang->hang_khach_hang === 'vang') {
            $nhomKhachHang = 'tiem_nang';
        }

        $khachHang->setAttribute('tong_doanh_thu', $tongDoanhThu);
        $khachHang->setAttribute('ty_le_hoan_tat', $tyLeHoanTat);
        $khachHang->setAttribute('nhom_khach_hang_hien_thi', $nhomKhachHang);
        $khachHang->setAttribute('co_thong_tin_lien_he', filled($khachHang->so_dien_thoai) || filled($khachHang->email));

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
        ];
    }

    private function dongBoKhachHangTuTaiKhoan(): void
    {
        $danhSachTaiKhoanKhachHang = NguoiDung::query()
            ->where('vai_tro', 'khach_hang')
            ->get(['ho_ten', 'email', 'so_dien_thoai', 'trang_thai']);

        foreach ($danhSachTaiKhoanKhachHang as $taiKhoanKhachHang) {
            $email = trim((string) ($taiKhoanKhachHang->email ?? ''));
            $soDienThoai = trim((string) ($taiKhoanKhachHang->so_dien_thoai ?? ''));

            if ($email === '' && $soDienThoai === '') {
                continue;
            }

            $khachHang = null;

            if ($email !== '') {
                $khachHang = KhachHang::query()
                    ->where('email', $email)
                    ->first();
            }

            if (!$khachHang && $email === '' && $soDienThoai !== '') {
                $khachHang = KhachHang::query()
                    ->where('so_dien_thoai', $soDienThoai)
                    ->first();
            }

            $trangThaiKhachHang = $taiKhoanKhachHang->trang_thai === 'tam_khoa'
                ? 'tam_khoa'
                : 'hoat_dong';

            if ($khachHang) {
                $khachHang->update([
                    'ho_ten' => $taiKhoanKhachHang->ho_ten,
                    'email' => $email !== '' ? $email : null,
                    'so_dien_thoai' => $soDienThoai !== '' ? $soDienThoai : null,
                    'trang_thai' => $trangThaiKhachHang,
                ]);

                continue;
            }

            KhachHang::query()->create([
                'ma_khach_hang' => $this->taoMaKhachHang(),
                'ho_ten' => $taiKhoanKhachHang->ho_ten,
                'email' => $email !== '' ? $email : null,
                'so_dien_thoai' => $soDienThoai !== '' ? $soDienThoai : null,
                'hang_khach_hang' => 'thuong',
                'trang_thai' => $trangThaiKhachHang,
            ]);
        }
    }

    private function taoMaKhachHang(): string
    {
        do {
            $maKhachHang = 'KH' . now()->format('ymdHis') . random_int(10, 99);
        } while (KhachHang::query()->where('ma_khach_hang', $maKhachHang)->exists());

        return $maKhachHang;
    }
}
