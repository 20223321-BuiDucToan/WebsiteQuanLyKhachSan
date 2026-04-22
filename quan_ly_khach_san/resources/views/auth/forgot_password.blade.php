@extends('layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
<div>
    <h1 class="auth-card-title">Quên mật khẩu</h1>
    <p class="auth-card-subtitle">Nhập đúng tên đăng nhập và email để nhận liên kết đặt lại mật khẩu.</p>

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên đăng nhập</label>
            <input
                type="text"
                name="ten_dang_nhap"
                class="form-control"
                value="{{ old('ten_dang_nhap') }}"
                placeholder="Nhập tên đăng nhập"
                required
            >
        </div>

        <div class="mb-4">
            <label class="form-label">Email tài khoản</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="Nhập email tài khoản"
                required
            >
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-paper-plane me-2"></i>Xác thực và tạo liên kết
        </button>

        @if(session('reset_link'))
            <div class="alert alert-info js-auto-dismiss-alert" data-auto-dismiss="5000">
                Xác thực thành công <br>
                <a href="{{ session('reset_link') }}" class="auth-link">Mở liên kết để đặt lại mật khẩu của bạn !</a>
            </div>
        @endif

        <div class="text-center">
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection
