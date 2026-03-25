@extends('layouts.admin')

@section('title', 'Trang chủ nhân viên')

@section('content')
    <div class="mb-4">
        <h2 class="section-title mb-1">Trang chủ nhân viên</h2>
        <p class="section-subtitle">Khu vực thao tác nghiệp vụ và hỗ trợ vận hành khách sạn</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card premium-card">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3">Xin chào, {{ $nguoiDung->ho_ten }}</h4>
                    <p class="text-muted mb-4">
                        Bạn đang đăng nhập với vai trò <strong>nhân viên</strong>. Khu vực này phục vụ cho các thao tác nghiệp vụ hằng ngày,
                        không bao gồm chức năng quản trị tài khoản nội bộ.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 text-center h-100">
                                <i class="fa-solid fa-calendar-check fs-2 mb-3 text-primary"></i>
                                <div class="fw-bold">Đặt phòng</div>
                                <div class="text-muted small">Tiếp nhận và xử lý yêu cầu đặt phòng.</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 text-center h-100">
                                <i class="fa-solid fa-users-line fs-2 mb-3 text-success"></i>
                                <div class="fw-bold">Khách hàng</div>
                                <div class="text-muted small">Tra cứu và cập nhật thông tin khách lưu trú.</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 text-center h-100">
                                <i class="fa-solid fa-receipt fs-2 mb-3 text-warning"></i>
                                <div class="fw-bold">Thanh toán</div>
                                <div class="text-muted small">Hỗ trợ lập hóa đơn và xử lý thanh toán.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin tài khoản</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Họ tên</div>
                        <div class="fw-bold">{{ $nguoiDung->ho_ten }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-bold">{{ $nguoiDung->email }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Tên đăng nhập</div>
                        <div class="fw-bold">{{ $nguoiDung->ten_dang_nhap }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Vai trò</div>
                        <div><span class="badge-role-nhan-vien">Nhân viên</span></div>
                    </div>

                    <div>
                        <div class="text-muted small">Lần đăng nhập cuối</div>
                        <div class="fw-bold">
                            {{ $nguoiDung->lan_dang_nhap_cuoi ? $nguoiDung->lan_dang_nhap_cuoi->format('d/m/Y H:i') : 'Chưa có dữ liệu' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection