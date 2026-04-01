@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản khách hàng')

@section('content')
<div>
    <h1 class="auth-card-title">Đăng ký</h1>
    <p class="auth-card-subtitle">Tạo tài khoản khách hàng để đặt phòng và theo dõi đơn đặt của bạn.</p>

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input
                type="text"
                name="ho_ten"
                class="form-control"
                value="{{ old('ho_ten') }}"
                placeholder="Nhập họ và tên"
                required
            >
        </div>

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

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="Nhập email"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input
                type="text"
                name="so_dien_thoai"
                class="form-control"
                value="{{ old('so_dien_thoai') }}"
                placeholder="Nhập số điện thoại"
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Tạo mật khẩu"
                required
            >
        </div>

        <div class="mb-4">
            <label class="form-label">Xác nhận mật khẩu</label>
            <input
                type="password"
                name="password_confirmation"
                class="form-control"
                placeholder="Nhập lại mật khẩu"
                required
            >
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-user-plus me-2"></i>Tạo tài khoản
        </button>

        <div class="text-center">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="auth-link">Đăng nhập</a>
        </div>
    </form>
</div>
@endsection
