<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DichVuController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'loai_dich_vu' => ['nullable', 'string', 'max:50'],
            'trang_thai' => ['nullable', Rule::in(['hoat_dong', 'tam_ngung'])],
        ]);

        $tuKhoa = $request->input('tu_khoa');
        $loaiDichVu = $request->input('loai_dich_vu');
        $trangThai = $request->input('trang_thai');

        $truyVanDichVu = DichVu::query()
            ->withCount('suDungDichVu')
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($innerQuery) use ($tuKhoa) {
                    $innerQuery
                        ->where('ma_dich_vu', 'like', "%{$tuKhoa}%")
                        ->orWhere('ten_dich_vu', 'like', "%{$tuKhoa}%")
                        ->orWhere('loai_dich_vu', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($loaiDichVu, fn($query) => $query->where('loai_dich_vu', $loaiDichVu))
            ->when($trangThai, fn($query) => $query->where('trang_thai', $trangThai));

        $danhSachDichVu = (clone $truyVanDichVu)
            ->orderBy('ten_dich_vu')
            ->paginate(12)
            ->withQueryString();

        $thongKe = [
            'tong' => DichVu::query()->count(),
            'hoat_dong' => DichVu::query()->where('trang_thai', 'hoat_dong')->count(),
            'tam_ngung' => DichVu::query()->where('trang_thai', 'tam_ngung')->count(),
            'da_phat_sinh' => DichVu::query()->has('suDungDichVu')->count(),
        ];

        $danhSachLoaiDichVu = DichVu::query()
            ->whereNotNull('loai_dich_vu')
            ->where('loai_dich_vu', '!=', '')
            ->distinct()
            ->orderBy('loai_dich_vu')
            ->pluck('loai_dich_vu');

        return view('dich_vu.index', compact(
            'danhSachDichVu',
            'danhSachLoaiDichVu',
            'tuKhoa',
            'loaiDichVu',
            'trangThai',
            'thongKe'
        ));
    }

    public function create()
    {
        return view('dich_vu.create', [
            'dichVu' => new DichVu(),
        ]);
    }

    public function store(Request $request)
    {
        $duLieu = $this->xacThucDichVu($request);
        $duLieu['ma_dich_vu'] = DichVu::taoMaMoi();

        DichVu::query()->create($duLieu);

        return redirect()
            ->route('dich-vu.index')
            ->with('success', 'Them dich vu thanh cong.');
    }

    public function edit(DichVu $dichVu)
    {
        return view('dich_vu.edit', compact('dichVu'));
    }

    public function update(Request $request, DichVu $dichVu)
    {
        $duLieu = $this->xacThucDichVu($request);

        $dichVu->update($duLieu);

        return redirect()
            ->route('dich-vu.index')
            ->with('success', 'Cap nhat dich vu thanh cong.');
    }

    public function destroy(DichVu $dichVu)
    {
        try {
            $dichVu->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('dich-vu.index')
                ->with('error', 'Khong the xoa dich vu da phat sinh su dung.');
        }

        return redirect()
            ->route('dich-vu.index')
            ->with('success', 'Xoa dich vu thanh cong.');
    }

    private function xacThucDichVu(Request $request): array
    {
        return $request->validate([
            'ten_dich_vu' => ['required', 'string', 'max:100'],
            'loai_dich_vu' => ['nullable', 'string', 'max:50'],
            'don_vi_tinh' => ['required', 'string', 'max:30'],
            'don_gia' => ['required', 'numeric', 'min:0'],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_ngung'])],
        ]);
    }
}
