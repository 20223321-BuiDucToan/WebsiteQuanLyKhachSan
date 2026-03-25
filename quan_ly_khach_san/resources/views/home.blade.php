@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <div class="section-title">Dashboard quản trị khách sạn</div>
    <div class="section-subtitle">
        Theo dõi nhanh hoạt động kinh doanh, đặt phòng và vận hành hệ thống.
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card gradient-1">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="mb-2" style="opacity:.9;">Tổng khách hàng</div>
                        <h2 class="fw-bold mb-0">120</h2>
                    </div>
                    <div class="stats-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <small>+12% so với tháng trước</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stats-card gradient-2">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="mb-2" style="opacity:.9;">Phòng đang hoạt động</div>
                        <h2 class="fw-bold mb-0">45</h2>
                    </div>
                    <div class="stats-icon">
                        <i class="fa-solid fa-bed"></i>
                    </div>
                </div>
                <small>Hiệu suất khai thác ổn định</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stats-card gradient-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="mb-2" style="opacity:.9;">Đơn đặt phòng</div>
                        <h2 class="fw-bold mb-0">30</h2>
                    </div>
                    <div class="stats-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <small>8 đơn mới trong hôm nay</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="card stats-card gradient-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="mb-2" style="opacity:.9;">Doanh thu tháng</div>
                        <h2 class="fw-bold mb-0">50tr</h2>
                    </div>
                    <div class="stats-icon">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <small>Mức tăng trưởng tích cực</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card panel-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Biểu đồ doanh thu 6 tháng</h5>
                        <small class="text-muted">Thống kê tổng quan doanh thu gần đây</small>
                    </div>
                </div>
                <canvas id="revenueChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card panel-card h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Tình trạng phòng</h5>
                <small class="text-muted">Phân bổ phòng hiện tại</small>

                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phòng trống</span>
                        <strong>18</strong>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 40%"></div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Phòng đã đặt</span>
                        <strong>20</strong>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: 44%"></div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Phòng bảo trì</span>
                        <strong>7</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" style="width: 16%"></div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-6">
                        <a href="#" class="quick-link">
                            <div class="quick-link-card text-center">
                                <div class="mb-2 fs-3 text-primary">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                                <div class="fw-semibold">Thêm khách</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-link">
                            <div class="quick-link-card text-center">
                                <div class="mb-2 fs-3 text-success">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </div>
                                <div class="fw-semibold">Đặt phòng</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-link">
                            <div class="quick-link-card text-center">
                                <div class="mb-2 fs-3 text-warning">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </div>
                                <div class="fw-semibold">Hóa đơn</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="quick-link">
                            <div class="quick-link-card text-center">
                                <div class="mb-2 fs-3 text-danger">
                                    <i class="fa-solid fa-gear"></i>
                                </div>
                                <div class="fw-semibold">Thiết lập</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card panel-card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Đặt phòng gần đây</h5>
                <small class="text-muted">Danh sách giao dịch mới nhất trong hệ thống</small>

                <div class="table-responsive mt-4">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Phòng</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#DP001</td>
                                <td>Nguyễn Văn A</td>
                                <td>Phòng 301</td>
                                <td><span class="status-badge status-success">Đã xác nhận</span></td>
                            </tr>
                            <tr>
                                <td>#DP002</td>
                                <td>Trần Thị B</td>
                                <td>Phòng 205</td>
                                <td><span class="status-badge status-warning">Chờ xử lý</span></td>
                            </tr>
                            <tr>
                                <td>#DP003</td>
                                <td>Lê Văn C</td>
                                <td>Phòng 402</td>
                                <td><span class="status-badge status-success">Đã xác nhận</span></td>
                            </tr>
                            <tr>
                                <td>#DP004</td>
                                <td>Phạm Thị D</td>
                                <td>Phòng 110</td>
                                <td><span class="status-badge status-warning">Chờ xử lý</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card panel-card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">Thông tin tài khoản</h5>
                <small class="text-muted">Thông tin người dùng hiện tại</small>

                <div class="mt-4">
                    <div class="mb-3">
                        <strong>Họ tên:</strong> {{ auth()->user()->ho_ten }}
                    </div>
                    <div class="mb-3">
                        <strong>Tên đăng nhập:</strong> {{ auth()->user()->ten_dang_nhap }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong> {{ auth()->user()->email }}
                    </div>
                    <div class="mb-3">
                        <strong>Vai trò:</strong>
                        {{ auth()->user()->vai_tro === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}
                    </div>
                    <div class="mb-3">
                        <strong>Trạng thái:</strong> {{ auth()->user()->trang_thai }}
                    </div>
                    <div class="mb-0">
                        <strong>Lần đăng nhập cuối:</strong>
                        {{ auth()->user()->lan_dang_nhap_cuoi ?? 'Chưa có dữ liệu' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
            datasets: [{
                label: 'Doanh thu',
                data: [12, 19, 15, 22, 28, 35],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563eb',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection