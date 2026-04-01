@extends('layouts.admin')

@section('title', 'Báo cáo thống kê')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Báo cáo thống kê</h2>
        <p class="section-subtitle">Tổng hợp hiệu suất kinh doanh và vận hành theo khoảng thời gian.</p>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Xem báo cáo</button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('bao-cao.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tổng đặt phòng</div>
                <div class="metric-value">{{ $tongDatPhong }}</div>
                <div class="small text-muted mt-2">Đã trả phòng: {{ $datPhongThanhCong }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tổng doanh thu hóa đơn</div>
                <div class="metric-value">{{ number_format((float) $tongDoanhThuHoaDon, 0, ',', '.') }}</div>
                <div class="small text-muted mt-2">VNĐ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đã thu / Công nợ</div>
                <div class="fw-bold text-success">{{ number_format((float) $tongDaThu, 0, ',', '.') }} VNĐ</div>
                <div class="fw-bold text-danger mt-1">{{ number_format((float) $congNo, 0, ',', '.') }} VNĐ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Tỷ lệ hủy / Công suất phòng</div>
                <div class="fw-bold text-danger">{{ number_format((float) $tyLeHuy, 2, ',', '.') }}%</div>
                <div class="fw-bold text-primary mt-1">{{ number_format((float) $congSuatPhong, 2, ',', '.') }}%</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Doanh thu 6 tháng gần nhất</h5>
                    <canvas id="chartDoanhThu" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Xu hướng đặt phòng 6 tháng</h5>
                    <canvas id="chartDatPhong" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Top phòng được đặt nhiều nhất</h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Phòng</th>
                            <th>Số lượt đặt</th>
                            <th>Tổng số đêm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topPhong as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->phong?->so_phong ? 'Phòng ' . $item->phong->so_phong : '-' }}</td>
                                <td>{{ $item->so_luot }}</td>
                                <td>{{ $item->tong_so_dem }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu top phòng trong khoảng đã chọn.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        const nhanDoanhThu = @json($nhanDoanhThu);
        const duLieuDoanhThu = @json($duLieuDoanhThu);
        const nhanDatPhong = @json($nhanDatPhong);
        const duLieuDatPhong = @json($duLieuDatPhong);

        const chartDoanhThu = document.getElementById('chartDoanhThu');
        if (chartDoanhThu) {
            new Chart(chartDoanhThu, {
                type: 'line',
                data: {
                    labels: nhanDoanhThu,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: duLieuDoanhThu,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, 0.16)',
                        tension: 0.35,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                },
            });
        }

        const chartDatPhong = document.getElementById('chartDatPhong');
        if (chartDatPhong) {
            new Chart(chartDatPhong, {
                type: 'bar',
                data: {
                    labels: nhanDatPhong,
                    datasets: [{
                        label: 'Số đơn đặt phòng',
                        data: duLieuDatPhong,
                        backgroundColor: '#2563eb',
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                },
            });
        }
    </script>
@endsection
