<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NguoiDungController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = $request->tu_khoa;
        $vaiTro = $request->vai_tro;
        $trangThai = $request->trang_thai;

        $danhSachNguoiDung = NguoiDung::query()
            ->when($tuKhoa, function ($query) use ($tuKhoa) {
                $query->where(function ($q) use ($tuKhoa) {
                    $q->where('ho_ten', 'like', "%{$tuKhoa}%")
                        ->orWhere('ten_dang_nhap', 'like', "%{$tuKhoa}%")
                        ->orWhere('email', 'like', "%{$tuKhoa}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($vaiTro, function ($query) use ($vaiTro) {
                $query->where('vai_tro', $vaiTro);
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('trang_thai', $trangThai);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('nguoi_dung.index', compact('danhSachNguoiDung', 'tuKhoa', 'vaiTro', 'trangThai'));
    }

    public function create()
    {
        return view('nguoi_dung.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => ['required', 'string', 'max:255'],
            'ten_dang_nhap' => ['required', 'string', 'max:100', 'unique:nguoi_dung,ten_dang_nhap'],
            'email' => ['required', 'email', 'max:255', 'unique:nguoi_dung,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'so_dien_thoai' => ['required', 'string', 'max:20'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'vai_tro' => ['required', Rule::in(['admin', 'nhan_vien', 'khach_hang'])],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_khoa'])],
        ]);

        $nguoiDung = NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'password' => $request->password,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'vai_tro' => $request->vai_tro,
            'trang_thai' => $request->trang_thai,
        ]);

        $this->dongBoKhachHangTheoTaiKhoan($nguoiDung);

        return redirect()->route('nguoi-dung.index')->with('success', 'Them nguoi dung thanh cong.');
    }

    public function show(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        return view('nguoi_dung.show', compact('nguoiDung'));
    }

    public function edit(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        return view('nguoi_dung.edit', compact('nguoiDung'));
    }

    public function update(Request $request, string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);
        $vaiTroTruocCapNhat = (string) $nguoiDung->vai_tro;
        $trangThaiMoi = (string) $request->input('trang_thai');
        $vaiTroMoi = (string) $request->input('vai_tro');

        $request->validate([
            'ho_ten' => ['required', 'string', 'max:255'],
            'ten_dang_nhap' => [
                'required',
                'string',
                'max:100',
                Rule::unique('nguoi_dung', 'ten_dang_nhap')->ignore($nguoiDung->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('nguoi_dung', 'email')->ignore($nguoiDung->id),
            ],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'so_dien_thoai' => ['required', 'string', 'max:20'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'vai_tro' => ['required', Rule::in(['admin', 'nhan_vien', 'khach_hang'])],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_khoa'])],
        ]);

        if (
            auth()->id() === $nguoiDung->id
            && ($vaiTroMoi !== 'admin' || $trangThaiMoi !== 'hoat_dong')
        ) {
            return redirect()->route('nguoi-dung.index')->with('error', 'Khong the tu ha quyen hoac tu khoa tai khoan admin dang dang nhap.');
        }

        if ($this->seLamMatAdminCuoi($nguoiDung, $vaiTroMoi, $trangThaiMoi)) {
            return redirect()->route('nguoi-dung.index')->with('error', 'He thong phai luon con it nhat mot admin hoat dong.');
        }

        $duLieuCapNhat = [
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'vai_tro' => $vaiTroMoi,
            'trang_thai' => $trangThaiMoi,
        ];

        if (! empty($request->password)) {
            $duLieuCapNhat['password'] = $request->password;
        }

        $nguoiDung->update($duLieuCapNhat);

        $this->dongBoKhachHangTheoTaiKhoan($nguoiDung->fresh('khachHang'), $vaiTroTruocCapNhat);

        return redirect()->route('nguoi-dung.index')->with('success', 'Cap nhat nguoi dung thanh cong.');
    }

    public function destroy(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        if (auth()->id() === $nguoiDung->id) {
            return redirect()->route('nguoi-dung.index')->with('error', 'Ban khong the tu xoa chinh minh.');
        }

        if ($this->seLamMatAdminCuoi($nguoiDung, 'deleted', 'deleted')) {
            return redirect()->route('nguoi-dung.index')->with('error', 'He thong phai luon con it nhat mot admin hoat dong.');
        }

        if ($nguoiDung->khachHang) {
            $nguoiDung->khachHang()->update(['nguoi_dung_id' => null]);
        }

        $nguoiDung->delete();

        return redirect()->route('nguoi-dung.index')->with('success', 'Xoa nguoi dung thanh cong.');
    }

    public function doiTrangThai(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        if (auth()->id() === $nguoiDung->id) {
            return redirect()->route('nguoi-dung.index')->with('error', 'Ban khong the tu khoa chinh minh.');
        }

        $trangThaiMoi = $nguoiDung->trang_thai === 'hoat_dong' ? 'tam_khoa' : 'hoat_dong';

        if ($this->seLamMatAdminCuoi($nguoiDung, (string) $nguoiDung->vai_tro, $trangThaiMoi)) {
            return redirect()->route('nguoi-dung.index')->with('error', 'He thong phai luon con it nhat mot admin hoat dong.');
        }

        $nguoiDung->trang_thai = $trangThaiMoi;
        $nguoiDung->save();

        $this->dongBoKhachHangTheoTaiKhoan($nguoiDung->fresh('khachHang'));

        return redirect()->route('nguoi-dung.index')->with('success', 'Doi trang thai tai khoan thanh cong.');
    }

    private function seLamMatAdminCuoi(NguoiDung $nguoiDung, string $vaiTroMoi, string $trangThaiMoi): bool
    {
        if ($nguoiDung->vai_tro !== 'admin' || $nguoiDung->trang_thai !== 'hoat_dong') {
            return false;
        }

        if ($vaiTroMoi === 'admin' && $trangThaiMoi === 'hoat_dong') {
            return false;
        }

        return NguoiDung::query()
            ->where('vai_tro', 'admin')
            ->where('trang_thai', 'hoat_dong')
            ->whereKeyNot($nguoiDung->id)
            ->doesntExist();
    }

    private function dongBoKhachHangTheoTaiKhoan(NguoiDung $nguoiDung, ?string $vaiTroTruocCapNhat = null): void
    {
        if ($nguoiDung->vai_tro === 'khach_hang') {
            KhachHang::dongBoTuTaiKhoan($nguoiDung);

            return;
        }

        if ($vaiTroTruocCapNhat === 'khach_hang' && $nguoiDung->khachHang) {
            $nguoiDung->khachHang()->update(['nguoi_dung_id' => null]);
        }
    }
}
