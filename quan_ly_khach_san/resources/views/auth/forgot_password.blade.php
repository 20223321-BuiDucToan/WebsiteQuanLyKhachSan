@extends('layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
<div>
    <h1 class="auth-card-title">Quên mật khẩu</h1>
    <p class="auth-card-subtitle">Nhập email đã đăng ký. Hệ thống sẽ gửi liên kết đặt lại mật khẩu nếu tài khoản hợp lệ.</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="form-label">Email tài khoản</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="ten@email.com"
                required
                autofocus
            >
            <div class="small text-muted mt-2">Vì lý do bảo mật, hệ thống không thông báo email có tồn tại hay không.</div>
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-paper-plane me-2"></i>Gửi liên kết đặt lại
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection
