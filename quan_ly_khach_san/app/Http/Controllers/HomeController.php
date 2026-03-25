<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;

class HomeController extends Controller
{
    public function index()
    {
        $nguoiDung = auth()->user();

        if ($nguoiDung->vai_tro === 'admin') {
            $tongNguoiDung = NguoiDung::count();
            $tongAdmin = NguoiDung::where('vai_tro', 'admin')->count();
            $tongNhanVien = NguoiDung::where('vai_tro', 'nhan_vien')->count();
            $tongTaiKhoanHoatDong = NguoiDung::where('trang_thai', 'hoat_dong')->count();

            $nguoiDungMoi = NguoiDung::latest('id')->take(5)->get();

            return view('dashboard.admin', compact(
                'tongNguoiDung',
                'tongAdmin',
                'tongNhanVien',
                'tongTaiKhoanHoatDong',
                'nguoiDungMoi'
            ));
        }

        return view('dashboard.nhan_vien', [
            'nguoiDung' => $nguoiDung,
        ]);
    }
}