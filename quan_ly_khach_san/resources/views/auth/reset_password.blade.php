@extends('layouts.auth')

@section('content')
<div>
    <div class="auth-card-title">Đặt lại mật khẩu</div>
    <div class="auth-card-subtitle">Tạo mật khẩu mới để bảo mật tài khoản của bạn.</div>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" placeholder="Nhập email">
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
        </div>

        <div class="mb-4">
            <label class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới">
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-key me-2"></i>Đặt lại mật khẩu
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection