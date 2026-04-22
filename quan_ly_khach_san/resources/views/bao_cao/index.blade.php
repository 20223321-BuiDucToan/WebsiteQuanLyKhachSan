@extends('layouts.admin')

@section('title', 'Báo cáo thống kê')

@push('styles')
    <style>
        .report-hero {
            border: none;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.28), transparent 28%),
                linear-gradient(135deg, #173652, #1d4ed8 52%, #0ea5e9);
            color: #fff;
        }

        .report-hero .section-title,
        .report-hero .section-subtitle {
            color: #fff;
        }

        .report-hero .section-subtitle {
            opacity: 0.84;
        }

        .hero-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .hero-meta-card {
            padding: 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .hero-meta-label {
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.8;
        }

        .hero-meta-value {
            margin-top: 6px;
            font-size: 1.4rem;
            font-weight: 800;
        }

        .hero-meta-note {
            margin-top: 4px;
            font-size: 0.82rem;
            opacity: 0.76;
        }

        .range-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #d5e3ef;
            background: #f8fbff;
            color: #35516d;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .range-pill:hover {
            color: #173652;
            background: #eef6ff;
        }

        .kpi-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 18px;
            background: #fff;
            height: 100%;
            box-shadow: 0 12px 24px rgba(15, 41, 68, 0.06);
        }

        .kpi-label {
            color: #68839f;
            font-size: 0.82rem;
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 1.6rem;
            line-height: 1.1;
            font-weight: 800;
            color: #102f49;
        }

        .trend-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .trend-pill--up {
            color: #166534;
            background: #dcfce7;
        }

        .trend-pill--down {
            color: #be123c;
            background: #ffe4e6;
        }

        .detail-list {
            display: grid;
            gap: 14px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #edf2f8;
        }

        .detail-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .status-list {
            display: grid;
            gap: 14px;
        }

        .status-item {
            display: grid;
            gap: 8px;
        }

        .status-item-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .status-bar {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: #e8eef4;
        }

        .status-bar > span {
            display: block;
            height: 100%;
            border-radius: inherit;
        }

        .status-bar--primary > span {
            background: linear-gradient(90deg, #1d4ed8, #38bdf8);
        }

        .status-bar--teal > span {
            background: linear-gradient(90deg, #0f766e, #14b8a6);
        }

        .insight-list {
            display: grid;
            gap: 12px;
        }

        .insight-item {
            display: flex;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #e5edf6;
            color: #35516d;
        }

        .insight-item i {
            margin-top: 3px;
            color: #0f766e;
        }

        .table-note {
            color: #68839f;
            font-size: 0.83rem;
        }

        .empty-state {
            border: 1px dashed #cad8e6;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #68839f;
            background: #fbfdff;
        }
    </style>
@endpush

@section('content')
    @php
        $quickRanges = [
            [
                'label' => '7 ngày gần đây',
                'query' => [
                    'tu_ngay' => now()->copy()->subDays(6)->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ],
            ],
            [
                'label' => '30 ngày gần đây',
                'query' => [
                    'tu_ngay' => now()->copy()->subDays(29)->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ],
            ],
            [
                'label' => 'Tháng này',
                'query' => [
                    'tu_ngay' => now()->copy()->startOfMonth()->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ],
            ],
            [
                'label' => '90 ngày',
                'query' => [
                    'tu_ngay' => now()->copy()->subDays(89)->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ],
            ],
        ];

        $formatBienDong = function (float $giaTri) {
            return [
                'text' => ($giaTri > 0 ? '+' : '') . number_format($giaTri, 1, ',', '.') . '%',
                'class' => $giaTri >= 0 ? 'trend-pill trend-pill--up' : 'trend-pill trend-pill--down',
                'icon' => $giaTri >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down',
            ];
        };

        $trendDoanhThu = $formatBienDong($bienDongDoanhThu);
        $trendDatPhong = $formatBienDong($bienDongDatPhong);
        $trendDongTien = $formatBienDong($bienDongDongTien);

        $kpiCards = [
            [
                'label' => 'Doanh thu phát sinh',
                'value' => number_format((float) $tongDoanhThuHoaDon, 0, ',', '.') . ' VNĐ',
                'note' => 'Tổng ' . $tongHoaDon . ' hóa đơn trong kỳ',
                'trend' => $trendDoanhThu,
            ],
            [
                'label' => 'Thu hồi hóa đơn kỳ này',
                'value' => number_format((float) $tongThuHoiHoaDonTrongKy, 0, ',', '.') . ' VNĐ',
                'note' => 'Tỷ lệ thu hồi ' . number_format((float) $tyLeThuHoi, 1, ',', '.') . '%',
                'trend' => null,
            ],
            [
                'label' => 'Dòng tiền thu trong kỳ',
                'value' => number_format((float) $dongTienThuTrongKy, 0, ',', '.') . ' VNĐ',
                'note' => 'So với kỳ trước',
                'trend' => $trendDongTien,
            ],
            [
                'label' => 'Tổng đặt phòng',
                'value' => $tongDatPhong,
                'note' => 'Hoàn tất ' . number_format((float) $tyLeHoanTatDatPhong, 1, ',', '.') . '%',
                'trend' => $trendDatPhong,
            ],
            [
                'label' => 'Hủy phòng',
                'value' => $datPhongDaHuy,
                'note' => 'Tỷ lệ hủy ' . number_format((float) $tyLeHuy, 1, ',', '.') . '%',
                'trend' => null,
            ],
            [
                'label' => 'Công suất phòng',
                'value' => number_format((float) $congSuatPhong, 1, ',', '.') . '%',
                'note' => $tongDemDaDat . '/' . $tongDemKhaDung . ' đêm phòng đã dùng',
                'trend' => null,
            ],
        ];
    @endphp

    <div class="premium-card report-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Báo cáo thống kê</h2>
                    <p class="section-subtitle">Theo dõi hiệu suất vận hành, doanh thu, công nợ và xu hướng đặt phòng theo từng giai đoạn.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="btn btn-light fw-semibold disabled">
                        <i class="fa-regular fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach($quickRanges as $range)
                    <a href="{{ route('bao-cao.index', $range['query']) }}" class="range-pill">
                        <i class="fa-regular fa-clock"></i>{{ $range['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hero-meta-grid">
                <div class="hero-meta-card">
                    <div class="hero-meta-label">Số ngày báo cáo</div>
                    <div class="hero-meta-value">{{ $soNgayBaoCao }}</div>
                    <div class="hero-meta-note">Khoảng thời gian đang phân tích</div>
                </div>
                <div class="hero-meta-card">
                    <div class="hero-meta-label">Tổng phòng</div>
                    <div class="hero-meta-value">{{ $tongPhong }}</div>
                    <div class="hero-meta-note">Năng lực phục vụ hiện tại</div>
                </div>
                <div class="hero-meta-card">
                    <div class="hero-meta-label">Đang xử lý</div>
                    <div class="hero-meta-value">{{ $datPhongDangXuLy }}</div>
                    <div class="hero-meta-note">Đơn đặt phòng chưa hoàn tất</div>
                </div>
                <div class="hero-meta-card">
                    <div class="hero-meta-label">Công nợ mở</div>
                    <div class="hero-meta-value">{{ number_format((float) $congNo, 0, ',', '.') }}</div>
                    <div class="hero-meta-note">VNĐ còn lại trên hóa đơn trong kỳ</div>
                </div>
            </div>
        </div>
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
        @foreach($kpiCards as $card)
            <div class="col-xl-4 col-md-6">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div class="kpi-label">{{ $card['label'] }}</div>
                        @if($card['trend'])
                            <span class="{{ $card['trend']['class'] }}">
                                <i class="fa-solid {{ $card['trend']['icon'] }}"></i>{{ $card['trend']['text'] }}
                            </span>
                        @endif
                    </div>
                    <div class="kpi-value">{{ $card['value'] }}</div>
                    <div class="table-note mt-2">{{ $card['note'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Doanh thu và dòng tiền {{ $moTaCotMoc }}</h5>
                            <p class="table-note mb-0">So sánh doanh thu phát sinh từ hóa đơn với tiền thu thực tế trong cùng khoảng báo cáo.</p>
                        </div>
                    </div>
                    <canvas id="chartCashflow" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Cấu phần doanh thu</h5>
                    <div class="detail-list">
                        <div class="detail-item">
                            <div>
                                <div class="fw-semibold">Tiền phòng</div>
                                <div class="table-note">Nguồn thu cốt lõi</div>
                            </div>
                            <div class="fw-bold">{{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div>
                                <div class="fw-semibold">Dịch vụ</div>
                                <div class="table-note">Giá trị cộng thêm</div>
                            </div>
                            <div class="fw-bold">{{ number_format((float) $tongTienDichVu, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div>
                                <div class="fw-semibold">Thuế</div>
                                <div class="table-note">Khoản cộng trên hóa đơn</div>
                            </div>
                            <div class="fw-bold">{{ number_format((float) $tongThue, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div>
                                <div class="fw-semibold">Giảm giá</div>
                                <div class="table-note">Khoản trừ đã áp dụng</div>
                            </div>
                            <div class="fw-bold text-danger">-{{ number_format((float) $tongGiamGia, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="detail-item">
                            <div>
                                <div class="fw-semibold">Giá trị trung bình / hóa đơn</div>
                                <div class="table-note">Hiệu quả trên mỗi hóa đơn</div>
                            </div>
                            <div class="fw-bold">{{ number_format((float) $giaTriHoaDonTrungBinh, 0, ',', '.') }} VNĐ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Đặt phòng và hủy phòng {{ $moTaCotMoc }}</h5>
                    <p class="table-note">Theo dõi xu hướng đơn vào và số lượng đơn hủy để đánh giá hiệu quả xác nhận đặt phòng.</p>
                    <canvas id="chartBookingTrend" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Nguồn đặt phòng</h5>
                    <canvas id="chartSource" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Phương thức thanh toán</h5>
                    <canvas id="chartPayment" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Gợi ý vận hành</h5>
                    <div class="insight-list">
                        @foreach($insights as $insight)
                            <div class="insight-item">
                                <i class="fa-solid fa-lightbulb"></i>
                                <div>{{ $insight }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Trạng thái đặt phòng</h5>
                    <div class="status-list">
                        @foreach($phanBoTrangThaiDatPhong as $item)
                            <div class="status-item">
                                <div class="status-item-top">
                                    <div>
                                        <div class="fw-semibold">{{ $item['label'] }}</div>
                                        <div class="table-note">{{ number_format((float) $item['percent'], 1, ',', '.') }}%</div>
                                    </div>
                                    <span class="chip chip-info">{{ $item['value'] }}</span>
                                </div>
                                <div class="status-bar status-bar--primary">
                                    <span style="width: {{ min(100, max(0, $item['percent'])) }}%"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Trạng thái hóa đơn</h5>
                    <div class="status-list">
                        @foreach($phanBoTrangThaiHoaDon as $item)
                            <div class="status-item">
                                <div class="status-item-top">
                                    <div>
                                        <div class="fw-semibold">{{ $item['label'] }}</div>
                                        <div class="table-note">{{ number_format((float) $item['percent'], 1, ',', '.') }}%</div>
                                    </div>
                                    <span class="chip chip-warning">{{ $item['value'] }}</span>
                                </div>
                                <div class="status-bar status-bar--teal">
                                    <span style="width: {{ min(100, max(0, $item['percent'])) }}%"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Top phòng hiệu quả</h5>
                            <p class="table-note mb-0">Xếp hạng theo tổng số đêm đặt trong kỳ.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Số lượt đặt</th>
                                    <th>Tổng số đêm</th>
                                    <th>Doanh thu phòng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPhong as $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $item->phong?->so_phong ? 'Phòng ' . $item->phong->so_phong : '-' }}</td>
                                        <td>{{ $item->so_luot }}</td>
                                        <td>{{ $item->tong_so_dem }}</td>
                                        <td class="fw-bold">{{ number_format((float) $item->doanh_thu_phong, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4">
                                            <div class="empty-state">Chưa có dữ liệu phòng nổi bật trong khoảng này.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Top khách hàng theo doanh thu</h5>
                            <p class="table-note mb-0">Tổng hợp từ các hóa đơn phát sinh trong kỳ báo cáo.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Hóa đơn</th>
                                    <th>Doanh thu</th>
                                    <th>Đã thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topKhachHang as $khachHang)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $khachHang->ho_ten }}</div>
                                            <div class="table-note">{{ $khachHang->so_dien_thoai ?: 'Chưa có số điện thoại' }}</div>
                                        </td>
                                        <td>{{ $khachHang->so_hoa_don }}</td>
                                        <td class="fw-bold">{{ number_format((float) $khachHang->doanh_thu, 0, ',', '.') }} VNĐ</td>
                                        <td>{{ number_format((float) $khachHang->da_thu, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4">
                                            <div class="empty-state">Chưa có khách hàng nổi bật trong khoảng báo cáo này.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        const nhanXuHuong = @json($nhanXuHuong);
        const duLieuDoanhThu = @json($duLieuDoanhThu);
        const duLieuThuTien = @json($duLieuThuTien);
        const duLieuDatPhong = @json($duLieuDatPhong);
        const duLieuDatHuy = @json($duLieuDatHuy);
        const nhanNguonDat = @json(collect($phanBoNguonDat)->pluck('label')->all());
        const duLieuNguonDat = @json(collect($phanBoNguonDat)->pluck('value')->all());
        const nhanThanhToan = @json(collect($phanBoThanhToan)->pluck('label')->all());
        const duLieuThanhToan = @json(collect($phanBoThanhToan)->pluck('value')->all());

        const chartCashflow = document.getElementById('chartCashflow');
        if (chartCashflow) {
            new Chart(chartCashflow, {
                type: 'line',
                data: {
                    labels: nhanXuHuong,
                    datasets: [
                        {
                            label: 'Doanh thu phát sinh',
                            data: duLieuDoanhThu,
                            borderColor: '#0f766e',
                            backgroundColor: 'rgba(15, 118, 110, 0.14)',
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Tiền thu thực tế',
                            data: duLieuThuTien,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            fill: true,
                            tension: 0.35,
                        }
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        }

        const chartBookingTrend = document.getElementById('chartBookingTrend');
        if (chartBookingTrend) {
            new Chart(chartBookingTrend, {
                type: 'bar',
                data: {
                    labels: nhanXuHuong,
                    datasets: [
                        {
                            label: 'Đặt phòng',
                            data: duLieuDatPhong,
                            backgroundColor: '#1d4ed8',
                            borderRadius: 8,
                        },
                        {
                            label: 'Hủy phòng',
                            data: duLieuDatHuy,
                            backgroundColor: '#e11d48',
                            borderRadius: 8,
                        }
                    ],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        }

        const doughnutColors = ['#2563eb', '#0f766e', '#f59e0b', '#e11d48', '#64748b'];

        const chartSource = document.getElementById('chartSource');
        if (chartSource) {
            new Chart(chartSource, {
                type: 'doughnut',
                data: {
                    labels: nhanNguonDat,
                    datasets: [{
                        data: duLieuNguonDat,
                        backgroundColor: doughnutColors,
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '62%',
                },
            });
        }

        const chartPayment = document.getElementById('chartPayment');
        if (chartPayment) {
            new Chart(chartPayment, {
                type: 'doughnut',
                data: {
                    labels: nhanThanhToan,
                    datasets: [{
                        data: duLieuThanhToan,
                        backgroundColor: ['#0f766e', '#2563eb', '#f59e0b', '#8b5cf6', '#64748b'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '62%',
                },
            });
        }
    </script>
@endsection
