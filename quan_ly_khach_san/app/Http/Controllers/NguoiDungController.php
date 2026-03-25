<?php

namespace App\Http\Controllers;

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
            'vai_tro' => ['required', Rule::in(['admin', 'nhan_vien'])],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_khoa'])],
        ]);

        NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'password' => $request->password,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'vai_tro' => $request->vai_tro,
            'trang_thai' => $request->trang_thai,
        ]);

        return redirect()->route('nguoi-dung.index')->with('success', 'Thêm người dùng thành công.');
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
            'vai_tro' => ['required', Rule::in(['admin', 'nhan_vien'])],
            'trang_thai' => ['required', Rule::in(['hoat_dong', 'tam_khoa'])],
        ]);

        $duLieuCapNhat = [
            'ho_ten' => $request->ho_ten,
            'ten_dang_nhap' => $request->ten_dang_nhap,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'vai_tro' => $request->vai_tro,
            'trang_thai' => $request->trang_thai,
        ];

        if (!empty($request->password)) {
            $duLieuCapNhat['password'] = $request->password;
        }

        $nguoiDung->update($duLieuCapNhat);

        return redirect()->route('nguoi-dung.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        if (auth()->id() == $nguoiDung->id) {
            return redirect()->route('nguoi-dung.index')->with('error', 'Bạn không thể tự xóa chính mình.');
        }

        $nguoiDung->delete();

        return redirect()->route('nguoi-dung.index')->with('success', 'Xóa người dùng thành công.');
    }

    public function doiTrangThai(string $id)
    {
        $nguoiDung = NguoiDung::findOrFail($id);

        if (auth()->id() == $nguoiDung->id) {
            return redirect()->route('nguoi-dung.index')->with('error', 'Bạn không thể tự khóa chính mình.');
        }

        $nguoiDung->trang_thai = $nguoiDung->trang_thai === 'hoat_dong' ? 'tam_khoa' : 'hoat_dong';
        $nguoiDung->save();

        return redirect()->route('nguoi-dung.index')->with('success', 'Đổi trạng thái tài khoản thành công.');
    }
}