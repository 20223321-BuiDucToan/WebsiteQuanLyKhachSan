@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div>
    <h1 class="auth-card-title">Đặt lại mật khẩu</h1>
    <p class="auth-card-subtitle">Tạo mật khẩu mới cho tài khoản của bạn. Liên kết chỉ dùng được một lần.</p>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email', $email) }}"
                readonly
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Tối thiểu 8 ký tự"
                required
                autofocus
            >
            <div class="small text-muted mt-2">Nên dùng chữ hoa, chữ thường, số và ký tự đặc biệt.</div>
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
