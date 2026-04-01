<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhong;
use App\Models\Phong;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        Phong::query()->create($duLieu);

        return redirect()
            ->route('phong.index')
            ->with('success', 'Them phong thanh cong.');
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
        $duLieu['ma_phong'] = $this->taoMaPhong($duLieu['so_phong'], $phong->id);

        $phong->update($duLieu);

        return redirect()
            ->route('phong.index')
            ->with('success', 'Cap nhat phong thanh cong.');
    }

    public function destroy(Phong $phong)
    {
        try {
            $phong->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('phong.index')
                ->with('error', 'Khong the xoa phong da phat sinh dat phong.');
        }

        return redirect()
            ->route('phong.index')
            ->with('success', 'Xoa phong thanh cong.');
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
            'trang_thai' => ['required', Rule::in(['trong', 'da_dat', 'dang_su_dung', 'bao_tri', 'don_dep'])],
            'tinh_trang_ve_sinh' => ['required', Rule::in(['sach', 'can_don', 'dang_don', 'ban'])],
            'tinh_trang_hoat_dong' => ['required', Rule::in(['hoat_dong', 'tam_ngung'])],
            'gia_mac_dinh' => ['nullable', 'numeric', 'min:0'],
            'ghi_chu' => ['nullable', 'string', 'max:1000'],
        ]);
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
                ->when($phongId, fn($query) => $query->where('id', '!=', $phongId))
                ->exists()
        ) {
            $maPhong = $baseCode . $soThuTu;
            $soThuTu++;
        }

        return $maPhong;
    }
}
