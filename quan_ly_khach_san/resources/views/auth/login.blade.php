@extends('layouts.auth')

@section('title', 'Đăng nhập hệ thống')

@section('content')
<div>
    <h1 class="auth-card-title">Đăng nhập</h1>
    <p class="auth-card-subtitle">Đăng nhập để đặt phòng trực tuyến hoặc truy cập khu vực quản lý.</p>

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email hoặc tên đăng nhập</label>
            <input
                type="text"
                name="login"
                class="form-control"
                value="{{ old('login') }}"
                placeholder="Nhập email hoặc tên đăng nhập"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Nhập mật khẩu"
                required
            >
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>

            <a href="{{ route('password.request') }}" class="auth-link">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
        </button>

        <div class="text-center">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="auth-link">Đăng ký ngay</a>
        </div>
    </form>
</div>
@endsection
