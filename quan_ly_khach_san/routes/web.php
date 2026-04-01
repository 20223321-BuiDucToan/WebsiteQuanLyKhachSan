<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DatPhongController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\BaoCaoThongKeController;
use App\Http\Controllers\PublicBookingController;

Route::get('/', [PublicBookingController::class, 'index'])->name('booking.index');
Route::post('/dat-phong', [PublicBookingController::class, 'store'])
    ->middleware(['auth', 'kiem_tra_tai_khoan_hoat_dong', 'kiem_tra_vai_tro:khach_hang'])
    ->name('booking.store');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');

    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'kiem_tra_tai_khoan_hoat_dong', 'kiem_tra_vai_tro:admin,nhan_vien'])->group(function () {
    Route::get('/trang-chu', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('/quan-ly-dat-phong')->name('dat-phong.')->group(function () {
        Route::get('/', [DatPhongController::class, 'index'])->name('index');
        Route::get('/tao-moi', [DatPhongController::class, 'create'])->name('create');
        Route::post('/', [DatPhongController::class, 'store'])->name('store');
        Route::patch('/{datPhong}/trang-thai', [DatPhongController::class, 'capNhatTrangThai'])->name('cap-nhat-trang-thai');
        Route::get('/{datPhong}', [DatPhongController::class, 'show'])->name('show');
    });

    Route::resource('khach-hang', KhachHangController::class)
        ->parameters(['khach-hang' => 'khachHang'])
        ->only(['index', 'show', 'edit', 'update']);
    Route::patch('/khach-hang/{khachHang}/doi-trang-thai', [KhachHangController::class, 'doiTrangThai'])
        ->name('khach-hang.doi-trang-thai');

    Route::prefix('/quan-ly-thanh-toan')->name('thanh-toan.')->group(function () {
        Route::get('/', [ThanhToanController::class, 'index'])->name('index');
        Route::post('/', [ThanhToanController::class, 'store'])->name('store');
    });

    Route::prefix('/quan-ly-hoa-don')->name('hoa-don.')->group(function () {
        Route::get('/', [HoaDonController::class, 'index'])->name('index');
        Route::get('/tao-moi', [HoaDonController::class, 'create'])->name('create');
        Route::post('/', [HoaDonController::class, 'store'])->name('store');
        Route::patch('/{hoaDon}/trang-thai', [HoaDonController::class, 'capNhatTrangThai'])->name('cap-nhat-trang-thai');
        Route::get('/{hoaDon}', [HoaDonController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'kiem_tra_tai_khoan_hoat_dong', 'kiem_tra_vai_tro:admin'])->group(function () {
    Route::resource('nguoi-dung', NguoiDungController::class);
    Route::patch('/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai'])
        ->name('nguoi-dung.doi-trang-thai');

    Route::resource('phong', PhongController::class)->except(['show']);
    Route::get('/bao-cao-thong-ke', [BaoCaoThongKeController::class, 'index'])->name('bao-cao.index');
});
