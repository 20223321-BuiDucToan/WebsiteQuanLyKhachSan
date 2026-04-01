@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Chi tiết người dùng</h2>
        <p class="section-subtitle">Thông tin đầy đủ của tài khoản nội bộ.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small">Họ tên</label>
                    <div class="fw-bold">{{ $nguoiDung->ho_ten }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Tên đăng nhập</label>
                    <div class="fw-bold">{{ $nguoiDung->ten_dang_nhap }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Email</label>
                    <div class="fw-bold">{{ $nguoiDung->email }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Số điện thoại</label>
                    <div class="fw-bold">{{ $nguoiDung->so_dien_thoai }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Vai trò</label>
                    <div class="fw-bold text-capitalize">{{ $nguoiDung->vai_tro }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Trạng thái</label>
                    <div class="fw-bold text-capitalize">{{ $nguoiDung->trang_thai }}</div>
                </div>

                <div class="col-md-12">
                    <label class="text-muted small">Địa chỉ</label>
                    <div class="fw-bold">{{ $nguoiDung->dia_chi ?: 'Chưa cập nhật' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Lần đăng nhập cuối</label>
                    <div class="fw-bold">{{ $nguoiDung->lan_dang_nhap_cuoi ? $nguoiDung->lan_dang_nhap_cuoi->format('d/m/Y H:i') : 'Chưa có' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Ngày tạo</label>
                    <div class="fw-bold">{{ $nguoiDung->created_at ? $nguoiDung->created_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('nguoi-dung.edit', $nguoiDung->id) }}" class="btn btn-warning text-white">
                    <i class="fa-solid fa-pen me-2"></i>Sửa
                </a>

                <a href="{{ route('nguoi-dung.index') }}" class="btn btn-soft">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
