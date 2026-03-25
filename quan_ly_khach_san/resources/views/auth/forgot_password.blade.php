@extends('layouts.auth')

@section('content')
<div>
    <div class="auth-card-title">Quên mật khẩu</div>
    <div class="auth-card-subtitle">Nhập email để nhận liên kết đặt lại mật khẩu.</div>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="form-label">Email tài khoản</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Nhập email">
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-paper-plane me-2"></i>Gửi yêu cầu
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection