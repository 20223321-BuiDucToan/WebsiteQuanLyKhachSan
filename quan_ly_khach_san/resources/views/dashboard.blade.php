@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-4">
        <h2 class="section-title mb-1">Dashboard</h2>
        <p class="section-subtitle">Tổng quan hệ thống quản lý khách sạn</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card gradient-1">
                <div class="stat-title">Tổng người dùng</div>
                <div class="stat-number">{{ $tongNguoiDung }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card gradient-2">
                <div class="stat-title">Tổng admin</div>
                <div class="stat-number">{{ $tongAdmin }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card gradient-3">
                <div class="stat-title">Tổng nhân viên</div>
                <div class="stat-number">{{ $tongNhanVien }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card gradient-4">
                <div class="stat-title">Tài khoản hoạt động</div>
                <div class="stat-number">{{ $tongTaiKhoanHoatDong }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Biểu đồ người dùng</h5>
                    <canvas id="userChart" height="130"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Người dùng mới</h5>

                    @forelse($nguoiDungMoi as $nguoiDung)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <div class="fw-bold">{{ $nguoiDung->ho_ten }}</div>
                                <div class="text-muted small">{{ $nguoiDung->email }}</div>
                            </div>
                            <div class="text-end">
                                <div class="small text-capitalize">{{ $nguoiDung->vai_tro }}</div>
                                <div class="text-muted small">{{ $nguoiDung->created_at->format('d/m/Y') }}</div>
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

@section('scripts')
<script>
    const ctx = document.getElementById('userChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Tổng người dùng', 'Admin', 'Nhân viên', 'Hoạt động'],
            datasets: [{
                label: 'Số lượng',
                data: [{{ $tongNguoiDung }}, {{ $tongAdmin }}, {{ $tongNhanVien }}, {{ $tongTaiKhoanHoatDong }}],
                borderRadius: 12
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
</script>
@endsection