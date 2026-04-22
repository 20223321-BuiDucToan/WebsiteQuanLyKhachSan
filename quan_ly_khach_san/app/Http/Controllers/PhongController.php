<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhong;
use App\Models\Phong;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PhongController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'loai_phong_id' => ['nullable', 'integer', 'exists:loai_phong,id'],
            'tang' => ['nullable', 'integer', 'min:0'],
            'trang_thai' => ['nullable', 'string', 'max:30'],
            'tinh_trang_hoat_dong' => ['nullable', 'string', 'max:30'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $loaiPhongId = $request->input('loai_phong_id');
        $tang = $request->input('tang');
        $trangThai = $request->input('trang_thai');
        $tinhTrangHoatDong = $request->input('tinh_trang_hoat_dong');

        $danhSachPhong = Phong::query()
            ->with('loaiPhong')
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_phong', 'like', "%{$tuKhoa}%")
                        ->orWhere('so_phong', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($loaiPhongId, function ($query) use ($loaiPhongId) {
                $query->where('loai_phong_id', $loaiPhongId);
            })
            ->when($tang !== null && $tang !== '', function ($query) use ($tang) {
                $query->where('tang', (int) $tang);
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            })
            ->when($tinhTrangHoatDong, function ($query) use ($tinhTrangHoatDong) {
                $query->where('tinh_trang_hoat_dong', $tinhTrangHoatDong);
            })
            ->orderBy('so_phong')
            ->paginate(12)
            ->withQueryString();

        $danhSachLoaiPhong = LoaiPhong::query()
            ->orderBy('ten_loai_phong')
            ->get();

        $thongKe = [
            'tong' => Phong::query()->count(),
            'trong' => Phong::query()->where('trang_thai', 'trong')->count(),
            'dang_su_dung' => Phong::query()->where('trang_thai', 'dang_su_dung')->count(),
            'bao_tri' => Phong::query()->where('trang_thai', 'bao_tri')->count(),
        ];

        return view('phong.index', compact(
            'danhSachPhong',
            'danhSachLoaiPhong',
            'tuKhoa',
            'loaiPhongId',
            'tang',
            'trangThai',
            'tinhTrangHoatDong',
            'thongKe'
        ));
    }

    public function create()
    {
        $danhSachLoaiPhong = LoaiPhong::query()
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('ten_loai_phong')
            ->get();

        return view('phong.create', [
            'danhSachLoaiPhong' => $danhSachLoaiPhong,
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $this->validateDuLieuPhong($request);
        $duLieu['ma_phong'] = $this->taoMaPhong($duLieu['so_phong']);
        $duLieu['trang_thai'] = Phong::TRANG_THAI_TRONG;

        $phong = Phong::query()->create($duLieu);
        $duLieuAnhPhong = $this->xuLyAnhPhong($request, $phong);

        if ($duLieuAnhPhong !== []) {
            $phong->update($duLieuAnhPhong);
        }

        $phong->refresh()->dongBoTrangThaiHeThong();

        return redirect()
            ->route('phong.index')
            ->with('success', 'Thêm phòng thành công.');
    }

    public function edit(Phong $phong)
    {
        $danhSachLoaiPhong = LoaiPhong::query()
            ->where('trang_thai', 'hoat_dong')
            ->orWhere('id', $phong->loai_phong_id)
            ->orderBy('ten_loai_phong')
            ->get();

        return view('phong.edit', [
            'phong' => $phong,
            'danhSachLoaiPhong' => $danhSachLoaiPhong,
        ]);
    }

    public function update(Request $request, Phong $phong)
    {
        $duLieu = $this->validateDuLieuPhong($request, $phong->id);

        if (($duLieu['tinh_trang_hoat_dong'] ?? null) === 'tam_ngung' && $phong->coDatPhongHoatDong()) {
            throw ValidationException::withMessages(['tinh_trang_hoat_dong' => 'Không thể chuyển phòng sang tạm ngưng khi phòng đang có đặt phòng hiệu lực.',
                'tinh_trang_hoat_dong' => 'Không thể chuyển phòng sang tạm ngưng khi phòng đang có đặt phòng hiệu lực.',
            ]);
        }

        $duLieu['ma_phong'] = $this->taoMaPhong($duLieu['so_phong'], $phong->id);
        $duLieuAnhPhong = $this->xuLyAnhPhong($request, $phong);

        $phong->update(array_merge($duLieu, $duLieuAnhPhong));

        $phong->refresh()->dongBoTrangThaiHeThong();

        return redirect()
            ->route('phong.index')
            ->with('success', 'Cập nhật phòng thành công.');
    }

    public function destroy(Phong $phong)
    {
        try {
            $danhSachAnhPhong = $phong->layDanhSachAnhPhong();
            $phong->delete();
            $this->xoaNhieuAnhPhong($danhSachAnhPhong);
        } catch (QueryException $exception) {
            return redirect()
                ->route('phong.index')
                ->with('error', 'Không thể xóa phòng đã phát sinh đặt phòng.');
        }

        return redirect()
            ->route('phong.index')
            ->with('success', 'Xóa phòng thành công.');
    }

    private function validateDuLieuPhong(Request $request, ?int $phongId = null): array
    {
        return $request->validate([
            'so_phong' => [
                'required',
                'string',
                'max:20',
                Rule::unique('phong', 'so_phong')->ignore($phongId),
            ],
            'loai_phong_id' => ['required', 'integer', 'exists:loai_phong,id'],
            'tang' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tinh_trang_ve_sinh' => ['required', Rule::in(['sach', 'can_don', 'dang_don', 'ban'])],
            'tinh_trang_hoat_dong' => ['required', Rule::in(['hoat_dong', 'tam_ngung'])],
            'gia_mac_dinh' => ['nullable', 'numeric', 'min:0'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
            'anh_phong' => ['nullable', 'array'],
            'anh_phong.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'xoa_anh_phong' => ['nullable', 'array'],
            'xoa_anh_phong.*' => ['nullable', 'string', 'max:255'],
        ], [
            'anh_phong.*.image' => 'Mỗi tệp tải lên phải là hình ảnh hợp lệ.',
            'anh_phong.*.mimes' => 'Ảnh phòng chỉ hỗ trợ định dạng JPG, JPEG, PNG hoặc WEBP.',
            'anh_phong.*.max' => 'Mỗi ảnh phòng chỉ được tối đa 4MB.',
        ]);
    }

    private function xuLyAnhPhong(Request $request, ?Phong $phong = null): array
    {
        $danhSachAnhPhong = $phong?->layDanhSachAnhPhong() ?? [];
        $anhCanXoa = array_values(array_filter(
            (array) $request->input('xoa_anh_phong', []),
            fn ($duongDan) => is_string($duongDan) && in_array($duongDan, $danhSachAnhPhong, true)
        ));

        if ($anhCanXoa !== []) {
            $this->xoaNhieuAnhPhong($anhCanXoa);
            $danhSachAnhPhong = array_values(array_diff($danhSachAnhPhong, $anhCanXoa));
        }

        if (!$request->hasFile('anh_phong')) {
            return $anhCanXoa !== [] ? ['anh_phong' => $danhSachAnhPhong] : [];
        }

        $thuMuc = public_path('uploads/phong');

        if (!File::isDirectory($thuMuc)) {
            File::makeDirectory($thuMuc, 0755, true);
        }

        foreach ((array) $request->file('anh_phong', []) as $tepAnh) {
            if (!$tepAnh) {
                continue;
            }

            $tenTep = 'phong-'
                . Str::slug((string) ($request->input('so_phong') ?: $phong?->so_phong ?: 'hotel-room'))
                . '-'
                . now()->format('YmdHis')
                . '-'
                . Str::lower(Str::random(8))
                . '.'
                . $tepAnh->getClientOriginalExtension();

            $tepAnh->move($thuMuc, $tenTep);
            $danhSachAnhPhong[] = 'uploads/phong/' . $tenTep;
        }

        return ['anh_phong' => $danhSachAnhPhong];
    }

    private function xoaNhieuAnhPhong(array $danhSachAnhPhong): void
    {
        foreach ($danhSachAnhPhong as $duongDanAnh) {
            $this->xoaAnhPhong($duongDanAnh);
        }
    }

    private function xoaAnhPhong(?string $duongDanAnh): void
    {
        if (!$duongDanAnh) {
            return;
        }

        $duongDanDayDu = public_path($duongDanAnh);

        if (File::exists($duongDanDayDu)) {
            File::delete($duongDanDayDu);
        }
    }

    private function taoMaPhong(string $soPhong, ?int $phongId = null): string
    {
        $slug = preg_replace('/[^a-zA-Z0-9]/', '', strtoupper($soPhong));
        $baseCode = 'PH' . ($slug ?: now()->format('ymdHis'));
        $maPhong = $baseCode;
        $soThuTu = 1;

        while (
            Phong::query()
                ->where('ma_phong', $maPhong)
                ->when($phongId, fn ($query) => $query->where('id', '!=', $phongId))
                ->exists()
        ) {
            $maPhong = $baseCode . $soThuTu;
            $soThuTu++;
        }

        return $maPhong;
    }
}
