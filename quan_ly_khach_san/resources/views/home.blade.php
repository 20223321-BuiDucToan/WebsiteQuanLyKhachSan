@extends('layouts.admin')

@section('title', 'Trang chủ nội bộ')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Trang chủ nội bộ</h2>
        <p class="section-subtitle">Phiên bản giao diện mới đã được đồng bộ cho toàn bộ màn hình quản trị.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <p class="mb-2">Để bắt đầu thao tác nhanh, bạn có thể dùng các nút dưới đây:</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('dat-phong.create') }}" class="btn btn-gradient">Tạo đơn đặt phòng</a>
                <a href="{{ route('hoa-don.create') }}" class="btn btn-soft">Tạo hóa đơn</a>
                <a href="{{ route('bao-cao.index') }}" class="btn btn-soft">Xem báo cáo</a>
            </div>
        </div>
    </div>
@endsection
