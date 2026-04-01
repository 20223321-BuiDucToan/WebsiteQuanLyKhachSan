@extends('layouts.admin')

@section('title', 'Cập nhật khách hàng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Cập nhật khách hàng {{ $khachHang->ma_khach_hang }}</h2>
        <p class="section-subtitle">Chỉnh sửa hồ sơ, hạng thành viên và trạng thái hoạt động.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('khach-hang.update', $khachHang) }}" method="POST">
                @csrf
                @method('PUT')

                @include('khach_hang._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient"><i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi</button>
                    <a href="{{ route('khach-hang.show', $khachHang) }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
