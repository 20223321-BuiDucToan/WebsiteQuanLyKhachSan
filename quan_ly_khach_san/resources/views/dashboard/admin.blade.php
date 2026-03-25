@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mb-4">
        <h2 class="section-title mb-1">Dashboard quản trị</h2>
        <p class="section-subtitle">Tổng quan vận hành và quản trị hệ thống khách sạn</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card premium-card p-4">
                <h6 class="text-muted">Tổng người dùng</h6>
                <h2 class="fw-bold">{{ $tongNguoiDung }}</h2>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card premium-card p-4">
                <h6 class="text-muted">Tổng admin</h6>
                <h2 class="fw-bold">{{ $tongAdmin }}</h2>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card premium-card p-4">
                <h6 class="text-muted">Tổng nhân viên</h6>
                <h2 class="fw-bold">{{ $tongNhanVien }}</h2>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card premium-card p-4">
                <h6 class="text-muted">Tài khoản hoạt động</h6>
                <h2 class="fw-bold">{{ $tongTaiKhoanHoatDong }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Khu vực quản trị</h5>
                    <p class="text-muted mb-4">Quản trị viên có quyền theo dõi toàn hệ thống, quản lý tài khoản nội bộ và kiểm soát dữ liệu vận hành.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('nguoi-dung.index') }}" class="text-decoration-none">
                                <div class="border rounded-4 p-4 h-100">
                                    <div class="fw-bold mb-2">
                                        <i class="fa-solid fa-users me-2 text-primary"></i>Quản lý người dùng
                                    </div>
                                    <div class="text-muted small">Xem danh sách, tạo mới, cập nhật, khóa tài khoản nội bộ.</div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-4 p-4 h-100">
                                <div class="fw-bold mb-2">
                                    <i class="fa-solid fa-chart-column me-2 text-success"></i>Báo cáo hệ thống
                                </div>
                                <div class="text-muted small">Theo dõi thống kê quản trị và dữ liệu vận hành tổng quát.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Người dùng mới</h5>

                    @forelse($nguoiDungMoi as $item)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="fw-bold">{{ $item->ho_ten }}</div>
                            <div class="text-muted small">{{ $item->email }}</div>
                            <div class="small mt-2">
                                @if($item->vai_tro === 'admin')
                                    <span class="badge-role-admin">Admin</span>
                                @else
                                    <span class="badge-role-nhan-vien">Nhân viên</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Chưa có dữ liệu người dùng mới.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection