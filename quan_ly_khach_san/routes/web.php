<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NguoiDungController;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
    return redirect()->route('login');
    });
    Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');

    // Tạm comment phần quên mật khẩu vì chưa tạo controller
    // Route::get('/quen-mat-khau', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    // Route::post('/quen-mat-khau', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    // Route::get('/dat-lai-mat-khau/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    // Route::post('/dat-lai-mat-khau', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'kiem_tra_tai_khoan_hoat_dong'])->group(function () {
    Route::get('/trang-chu', [HomeController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'kiem_tra_tai_khoan_hoat_dong', 'kiem_tra_vai_tro:admin'])->group(function () {
    Route::resource('nguoi-dung', NguoiDungController::class);

    Route::patch('/nguoi-dung/{id}/doi-trang-thai', [NguoiDungController::class, 'doiTrangThai'])
        ->name('nguoi-dung.doi-trang-thai');
});