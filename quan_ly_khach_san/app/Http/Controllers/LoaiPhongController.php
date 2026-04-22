<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhong;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoaiPhongController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', Rule::in([LoaiPhong::TRANG_THAI_HOAT_DONG, LoaiPhong::TRANG_THAI_TAM_NGUNG])],
            'so_nguoi_toi_da' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $trangThai = $request->input('trang_thai');
        $soNguoiToiDa = $request->input('so_nguoi_toi_da');

        $truyVanLoaiPhong = LoaiPhong::query()
            ->withCount('phong')
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_loai_phong', 'like', "%{$tuKhoa}%")
                        ->orWhere('ten_loai_phong', 'like', "%{$tuKhoa}%")
                        ->orWhere('loai_giuong', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($trangThai, fn ($query) => $query->where('trang_thai', $trangThai))
            ->when($soNguoiToiDa, fn ($query) => $query->where('so_nguoi_toi_da', '>=', (int) $soNguoiToiDa));

        $danhSachLoaiPhong = (clone $truyVanLoaiPhong)
            ->orderBy('gia_mot_dem')
            ->orderBy('ten_loai_phong')
            ->paginate(12)
            ->withQueryString();

        $thongKe = [
            'tong' => LoaiPhong::query()->count(),
            'hoat_dong' => LoaiPhong::query()->where('trang_thai', LoaiPhong::TRANG_THAI_HOAT_DONG)->count(),
            'tam_ngung' => LoaiPhong::query()->where('trang_thai', LoaiPhong::TRANG_THAI_TAM_NGUNG)->count(),
            'dang_su_dung' => LoaiPhong::query()->has('phong')->count(),
        ];

        return view('loai_phong.index', compact(
            'danhSachLoaiPhong',
            'thongKe',
            'tuKhoa',
            'trangThai',
            'soNguoiToiDa',
        ));
    }

    public function create()
    {
        return view('loai_phong.create', [
            'loaiPhong' => new LoaiPhong(),
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $this->xacThucLoaiPhong($request);
        $duLieu['ma_loai_phong'] = LoaiPhong::taoMaMoi();

        LoaiPhong::query()->create($duLieu);

        return redirect()
            ->route('loai-phong.index')
            ->with('success', 'Thêm loại phòng thành công.');
    }

    public function edit(LoaiPhong $loaiPhong)
    {
        return view('loai_phong.edit', compact('loaiPhong'));
    }

    public function update(Request $request, LoaiPhong $loaiPhong)
    {
        $duLieu = $this->xacThucLoaiPhong($request, $loaiPhong);

        $loaiPhong->update($duLieu);

        return redirect()
            ->route('loai-phong.index')
            ->with('success', 'Cập nhật loại phòng thành công.');
    }

    public function destroy(LoaiPhong $loaiPhong)
    {
        if ($loaiPhong->phong()->exists()) {
            return redirect()
                ->route('loai-phong.index')
                ->with('error', 'Không thể xóa loại phòng đã được gán cho phòng đang tồn tại.');
        }

        $loaiPhong->delete();

        return redirect()
            ->route('loai-phong.index')
            ->with('success', 'Xóa loại phòng thành công.');
    }

    private function xacThucLoaiPhong(Request $request, ?LoaiPhong $loaiPhong = null): array
    {
        $duLieu = $request->validate([
            'ten_loai_phong' => [
                'required',
                'string',
                'max:100',
                Rule::unique('loai_phong', 'ten_loai_phong')->ignore($loaiPhong?->id),
            ],
            'mo_ta' => ['nullable', 'string', 'max:2000'],
            'gia_mot_dem' => ['required', 'numeric', 'min:0'],
            'so_nguoi_toi_da' => ['required', 'integer', 'min:1', 'max:20'],
            'dien_tich' => ['nullable', 'numeric', 'min:0'],
            'so_giuong' => ['required', 'integer', 'min:1', 'max:20'],
            'loai_giuong' => ['nullable', 'string', 'max:50'],
            'so_phong_tam' => ['required', 'integer', 'min:1', 'max:20'],
            'trang_thai' => ['required', Rule::in([LoaiPhong::TRANG_THAI_HOAT_DONG, LoaiPhong::TRANG_THAI_TAM_NGUNG])],
        ]);

        $duLieu['co_ban_cong'] = $request->boolean('co_ban_cong');
        $duLieu['co_bep_rieng'] = $request->boolean('co_bep_rieng');
        $duLieu['co_huong_bien'] = $request->boolean('co_huong_bien');

        return $duLieu;
    }
}
