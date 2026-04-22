<?php

namespace App\Http\Controllers;

use App\Models\DatPhong;
use App\Models\HoaDon;
use App\Models\KhachHang;
use App\Models\NguoiDung;
use App\Models\Phong;
use App\Models\ThanhToan;

class HomeController extends Controller
{
    public function index()
    {
        $nguoiDung = auth()->user();

        if ($nguoiDung->vai_tro === 'khach_hang') {
            return redirect()->route('booking.account');
        }

        if ($nguoiDung->vai_tro === 'admin') {
            $tongNguoiDung = NguoiDung::count();
            $tongAdmin = NguoiDung::where('vai_tro', 'admin')->count();
            $tongNhanVien = NguoiDung::where('vai_tro', 'nhan_vien')->count();
            $tongTaiKhoanHoatDong = NguoiDung::where('trang_thai', 'hoat_dong')->count();
            $tongDatPhong = DatPhong::count();
            $tongDatPhongChoXacNhan = DatPhong::where('trang_thai', 'cho_xac_nhan')->count();
            $tongDatPhongOnline = DatPhong::where('nguon_dat', 'website')->count();
            $tongPhong = Phong::count();
            $tongPhongDangSuDung = Phong::where('trang_thai', 'dang_su_dung')->count();
            $tongHoaDon = HoaDon::count();
            $tongHoaDonCanThu = HoaDon::whereIn('trang_thai', ['chua_thanh_toan', 'thanh_toan_mot_phan'])->count();
            $tongYeuCauThanhToanChoXuLy = ThanhToan::where('trang_thai', 'cho_xu_ly')
                ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
                ->count();
            $doanhThuThangNay = (float) ThanhToan::where('trang_thai', 'thanh_cong')
                ->whereBetween('thoi_diem_thanh_toan', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('so_tien');
            $tongKhachHang = KhachHang::count();

            $nguoiDungMoi = NguoiDung::latest('id')->take(5)->get();
            $datPhongGanDay = DatPhong::with(['khachHang', 'chiTietDatPhong.phong'])->latest('id')->take(5)->get();
            $danhSachThanhToanChoXuLy = ThanhToan::query()
                ->with(['hoaDon.datPhong.khachHang'])
                ->where('trang_thai', 'cho_xu_ly')
                ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
                ->latest('id')
                ->take(5)
                ->get();

            return view('dashboard.admin', compact(
                'tongNguoiDung',
                'tongAdmin',
                'tongNhanVien',
                'tongTaiKhoanHoatDong',
                'tongDatPhong',
                'tongDatPhongChoXacNhan',
                'tongDatPhongOnline',
                'tongPhong',
                'tongPhongDangSuDung',
                'tongHoaDon',
                'tongHoaDonCanThu',
                'tongYeuCauThanhToanChoXuLy',
                'doanhThuThangNay',
                'tongKhachHang',
                'nguoiDungMoi',
                'datPhongGanDay',
                'danhSachThanhToanChoXuLy'
            ));
        }

        $tongDatPhongChoXacNhan = DatPhong::where('trang_thai', 'cho_xac_nhan')->count();
        $tongDatPhongHomNay = DatPhong::whereDate('ngay_dat', now()->toDateString())->count();
        $tongKhachHang = KhachHang::count();
        $tongHoaDonCanThu = HoaDon::whereIn('trang_thai', ['chua_thanh_toan', 'thanh_toan_mot_phan'])->count();
        $tongYeuCauThanhToanChoXuLy = ThanhToan::where('trang_thai', 'cho_xu_ly')
            ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
            ->count();
        $tongTienThuHomNay = (float) ThanhToan::where('trang_thai', 'thanh_cong')
            ->whereDate('thoi_diem_thanh_toan', now()->toDateString())
            ->sum('so_tien');

        $danhSachCanXuLy = DatPhong::with(['khachHang', 'chiTietDatPhong.phong'])
            ->whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan'])
            ->latest('id')
            ->take(6)
            ->get();
        $danhSachThanhToanChoXuLy = ThanhToan::query()
            ->with(['hoaDon.datPhong.khachHang'])
            ->where('trang_thai', 'cho_xu_ly')
            ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard.nhan_vien', [
            'nguoiDung' => $nguoiDung,
            'tongDatPhongChoXacNhan' => $tongDatPhongChoXacNhan,
            'tongDatPhongHomNay' => $tongDatPhongHomNay,
            'tongKhachHang' => $tongKhachHang,
            'tongHoaDonCanThu' => $tongHoaDonCanThu,
            'tongYeuCauThanhToanChoXuLy' => $tongYeuCauThanhToanChoXuLy,
            'tongTienThuHomNay' => $tongTienThuHomNay,
            'danhSachCanXuLy' => $danhSachCanXuLy,
            'danhSachThanhToanChoXuLy' => $danhSachThanhToanChoXuLy,
        ]);
    }
}
