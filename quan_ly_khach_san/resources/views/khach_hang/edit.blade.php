@extends('layouts.admin')

@section('title', 'Cập nhật khách hàng')

@section('content')
    <div class="form-page">
        <div class="form-page-header">
            <div>
                <h2 class="form-page-title">Cập nhật khách hàng {{ $khachHang->ma_khach_hang }}</h2>
                <p class="form-page-subtitle">Mẫu nhập liệu được rút gọn để hồ sơ khách hàng dễ đọc và dễ sửa hơn.</p>
            </div>
        </div>

        <div class="form-shell">
            <div class="form-shell__body">
                <form action="{{ route('khach-hang.update', $khachHang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('khach_hang._form')

                    <div class="form-actions">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu hồ sơ
                        </button>
                        <a href="{{ route('khach-hang.show', $khachHang) }}" class="btn btn-soft">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
