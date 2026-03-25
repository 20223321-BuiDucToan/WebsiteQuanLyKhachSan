@extends('layouts.auth')

@section('content')
<div>
    <div class="auth-card-title">Đăng ký tài khoản</div>
    <div class="auth-card-subtitle">Tạo tài khoản mới để sử dụng hệ thống.</div>

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Họ tên</label>
                <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten') }}" placeholder="Nhập họ tên">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="ten_dang_nhap" class="form-control" value="{{ old('ten_dang_nhap') }}" placeholder="Tên đăng nhập">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai') }}" placeholder="Số điện thoại">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Nhập email">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
            </div>

            <div class="col-md-6 mb-4">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu">
            </div>
        </div>

        <button type="submit" class="btn btn-auth w-100 mb-3">
            <i class="fa-solid fa-user-plus me-2"></i>Đăng ký
        </button>

        <div class="text-center">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="auth-link">Quay lại đăng nhập</a>
        </div>
    </form>
</div>
@endsection