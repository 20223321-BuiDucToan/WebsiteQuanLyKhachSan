@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div>
    <h1 class="auth-card-title">Đặt lại mật khẩu</h1>
    <p class="auth-card-subtitle">Nhập thông tin xác thực và mật khẩu mới để khôi phục tài khoản.</p>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input
                type="text"
                name="ten_dang_nhap"
                class="form-control"
                value="{{ old('ten_dang_nhap', $ten_dang_nhap) }}"
                placeholder="Nhập tên đăng nhập"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email', $email) }}"
                placeholder="Nhập email"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Nhập mật khẩu mới"
                required
            >
        </div>

        <div class="mb-4">
            <label class="form-label">Xác nhận mật khẩu mới</label>
            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                placeholder="Nhập lại mật khẩu mới"
                required
            >
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-key me-2"></i>Cập nhật mật khẩu
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection
