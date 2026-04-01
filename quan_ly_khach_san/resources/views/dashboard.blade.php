@extends('layouts.admin')

@section('title', 'Trang tổng quan')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Trang tổng quan</h2>
        <p class="section-subtitle">Màn hình này đang được giữ để tương thích các route cũ của dự án.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <p class="mb-3">Bạn có thể truy cập các khu vực chính từ menu bên trái:</p>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route('dat-phong.index') }}" class="d-block border rounded-4 p-3">Quản lý đặt phòng</a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route('khach-hang.index') }}" class="d-block border rounded-4 p-3">Quản lý khách hàng</a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route('hoa-don.index') }}" class="d-block border rounded-4 p-3">Quản lý hóa đơn</a>
                </div>
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route('thanh-toan.index') }}" class="d-block border rounded-4 p-3">Quản lý thanh toán</a>
                </div>
            </div>
        </div>
    </div>
@endsection
