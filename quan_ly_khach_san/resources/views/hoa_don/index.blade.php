@extends('layouts.admin')

@section('title', 'Quản lý hóa đơn')

@push('styles')
    <style>
        .invoice-hero {
            border: none;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 34%),
                linear-gradient(135deg, #0f2944, #0f766e 58%, #14b8a6);
            color: #fff;
        }

        .invoice-hero .section-title,
        .invoice-hero .section-subtitle {
            color: #fff;
        }

        .invoice-hero .section-subtitle {
            opacity: 0.82;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .hero-stat-card {
            border-radius: 18px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.84;
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.55rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            opacity: 0.78;
        }

        .quick-filter {
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

        .quick-filter:hover {
            background: #eef7ff;
            color: #14314d;
        }

        .active-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef8f6;
            color: #0f5f58;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .collection-track {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: #e7edf4;
        }

        .collection-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0f766e, #14b8a6);
        }

        .table-invoice-code {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .metric-mini {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .metric-mini:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .metric-mini strong {
            font-size: 1.1rem;
        }

        .attention-list {
            display: grid;
            gap: 12px;
        }

        .attention-item {
            display: block;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid #e3ebf3;
            background: #fbfdff;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .attention-item:hover {
            transform: translateY(-2px);
            border-color: #c7dae9;
            box-shadow: 0 12px 24px rgba(15, 41, 68, 0.08);
        }

        .attention-item--urgent {
            border-color: #fecdd3;
            background: linear-gradient(180deg, #fff7f7, #ffffff);
        }

        .attention-title {
            font-weight: 700;
            color: #173652;
        }

        .amount-main {
            font-size: 1rem;
            font-weight: 800;
            color: #102f49;
        }

        .empty-state {
            border: 1px dashed #cad8e6;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #68839f;
            background: #fbfdff;
        }

        @media (max-width: 767px) {
            .hero-stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $quickFilters = [
            [
                'label' => 'Hôm nay',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), [
                    'tu_ngay' => now()->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ])),
            ],
            [
                'label' => '7 ngày',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), [
                    'tu_ngay' => now()->copy()->subDays(6)->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ])),
            ],
            [
                'label' => '30 ngày',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), [
                    'tu_ngay' => now()->copy()->subDays(29)->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ])),
            ],
            [
                'label' => 'Tháng này',
                'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), [
                    'tu_ngay' => now()->copy()->startOfMonth()->toDateString(),
                    'den_ngay' => now()->toDateString(),
                ])),
            ],
        ];

        $coBoLoc = filled($tuKhoa) || filled($trangThai) || filled($tuNgay) || filled($denNgay);

        $tongHoaDonCoTrangThai = max(1, $thongKe['tong']);

        $trangThaiTongHop = [
            ['label' => 'Chưa thanh toán', 'value' => $thongKe['chua_thanh_toan'], 'class' => 'chip chip-danger'],
            ['label' => 'Thanh toán một phần', 'value' => $thongKe['thanh_toan_mot_phan'], 'class' => 'chip chip-warning'],
            ['label' => 'Đã thanh toán', 'value' => $thongKe['da_thanh_toan'], 'class' => 'chip chip-success'],
            ['label' => 'Đã hủy', 'value' => $thongKe['da_huy'], 'class' => 'chip chip-neutral'],
        ];

        $mapTrangThai = [
            'chua_thanh_toan' => 'chip chip-danger',
            'thanh_toan_mot_phan' => 'chip chip-warning',
            'da_thanh_toan' => 'chip chip-success',
            'da_huy' => 'chip chip-neutral',
        ];
    @endphp

    <div class="premium-card invoice-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quản lý hóa đơn</h2>
                    <p class="section-subtitle">Theo dõi doanh thu phát sinh, tốc độ thu tiền và các hóa đơn cần ưu tiên xử lý.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('thanh-toan.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-credit-card me-2"></i>Quản lý thanh toán
                    </a>
                    <a href="{{ route('hoa-don.create') }}" class="btn btn-outline-light fw-semibold">
                        <i class="fa-solid fa-plus me-2"></i>Tạo hóa đơn
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng hóa đơn</div>
                    <div class="hero-stat-value">{{ $thongKe['tong'] }}</div>
                    <div class="hero-stat-note">Giá trị trung bình {{ number_format((float) $thongKe['gia_tri_trung_binh'], 0, ',', '.') }} VNĐ</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Doanh thu trên hóa đơn</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKe['tong_gia_tri'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">Tổng giá trị đang theo dõi trong bộ lọc hiện tại</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Đã thu hồi</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKe['da_thu'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">Tỷ lệ thu hồi {{ number_format((float) $thongKe['ty_le_thu_hoi'], 1, ',', '.') }}%</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Còn phải thu</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKe['con_lai'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">{{ $thongKe['can_thu_gap'] }} hóa đơn cần ưu tiên thu gấp</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($quickFilters as $filter)
                    <a href="{{ route('hoa-don.index', $filter['query']) }}" class="quick-filter">
                        <i class="fa-regular fa-clock"></i>{{ $filter['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="GET" class="row g-3">
                <div class="col-xl-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã hóa đơn, mã đặt phòng, tên khách">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="chua_thanh_toan" @selected($trangThai === 'chua_thanh_toan')>Chưa thanh toán</option>
                        <option value="thanh_toan_mot_phan" @selected($trangThai === 'thanh_toan_mot_phan')>Thanh toán một phần</option>
                        <option value="da_thanh_toan" @selected($trangThai === 'da_thanh_toan')>Đã thanh toán</option>
                        <option value="da_huy" @selected($trangThai === 'da_huy')>Đã hủy</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}">
                </div>

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <a href="{{ route('hoa-don.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>

            @if($coBoLoc)
                <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                    @if($tuKhoa)
                        <span class="active-filter-chip"><i class="fa-solid fa-magnifying-glass"></i>{{ $tuKhoa }}</span>
                    @endif
                    @if($trangThai)
                        <span class="active-filter-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($trangThai) }}</span>
                    @endif
                    @if($tuNgay || $denNgay)
                        <span class="active-filter-chip">
                            <i class="fa-regular fa-calendar"></i>{{ $tuNgay ?: '...' }} - {{ $denNgay ?: '...' }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xxl-8">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Danh sách hóa đơn</h5>
                            <div class="text-muted small">
                                {{ $thongKe['tong'] }} hóa đơn trong bộ lọc hiện tại
                                • Tổng giá trị {{ number_format((float) $thongKe['tong_gia_tri'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="small text-muted mb-2">Tiến độ thu hồi</div>
                            <div class="fw-bold">{{ number_format((float) $thongKe['ty_le_thu_hoi'], 1, ',', '.') }}%</div>
                        </div>
                    </div>

                    <div class="collection-track mb-4">
                        <div class="collection-bar" style="width: {{ min(100, max(0, $thongKe['ty_le_thu_hoi'])) }}%"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Hóa đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Lưu trú</th>
                                    <th>Giá trị và thu tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Xuất lúc</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachHoaDon as $hoaDon)
                                    @php
                                        $khachHang = $hoaDon->datPhong?->khachHang;
                                        $chip = $mapTrangThai[$hoaDon->trang_thai_hien_thi] ?? 'chip chip-neutral';
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="table-invoice-code">{{ $hoaDon->ma_hoa_don }}</div>
                                            <div class="table-subtext">Đơn đặt phòng: {{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $khachHang?->ho_ten ?? '-' }}</div>
                                            <div class="table-subtext">{{ $khachHang?->so_dien_thoai ?? ($khachHang?->email ?? '-') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ optional($hoaDon->datPhong?->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                                -
                                                {{ optional($hoaDon->datPhong?->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}
                                            </div>
                                            <div class="table-subtext">{{ $hoaDon->tong_so_phong }} phòng • {{ $hoaDon->tong_so_dem }} đêm</div>
                                        </td>
                                        <td style="min-width: 240px;">
                                            <div class="amount-main">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</div>
                                            <div class="table-subtext mb-2">
                                                Đã thu {{ number_format((float) $hoaDon->so_tien_da_thu, 0, ',', '.') }} VNĐ
                                                • Còn lại {{ number_format((float) $hoaDon->so_tien_con_lai, 0, ',', '.') }} VNĐ
                                            </div>
                                            <div class="collection-track">
                                                <div class="collection-bar" style="width: {{ min(100, max(0, $hoaDon->phan_tram_thanh_toan)) }}%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2">
                                                <span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai_hien_thi) }}</span>
                                            </div>

                                            @if($hoaDon->can_thu_gap)
                                                <span class="chip chip-danger">Cần thu gấp</span>
                                            @elseif((float) $hoaDon->so_tien_con_lai > 0)
                                                <span class="chip chip-warning">Đang theo dõi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ optional($hoaDon->thoi_diem_xuat)->format('d/m/Y H:i') ?? '-' }}</div>
                                            <div class="table-subtext">
                                                Hạn theo dõi:
                                                {{ optional($hoaDon->ngay_den_han_thu)->format('d/m/Y') ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('hoa-don.show', $hoaDon) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>Xem
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4">
                                            <div class="empty-state">
                                                Không có hóa đơn nào khớp với bộ lọc hiện tại.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $danhSachHoaDon->links() }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Cần chú ý</h5>
                            <p class="text-muted small mb-0">Ưu tiên các hóa đơn còn mở và đã qua mốc lưu trú.</p>
                        </div>
                        <span class="chip chip-danger">{{ $thongKe['can_thu_gap'] }} gấp</span>
                    </div>

                    <div class="attention-list">
                        @forelse($hoaDonCanChuY as $hoaDon)
                            <a href="{{ route('hoa-don.show', $hoaDon) }}" class="attention-item {{ $hoaDon->can_thu_gap ? 'attention-item--urgent' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="attention-title">{{ $hoaDon->ma_hoa_don }}</div>
                                        <div class="table-subtext">{{ $hoaDon->datPhong?->khachHang?->ho_ten ?? 'Chưa có khách hàng' }}</div>
                                    </div>
                                    <span class="{{ $hoaDon->can_thu_gap ? 'chip chip-danger' : 'chip chip-warning' }}">
                                        {{ $hoaDon->can_thu_gap ? 'Ưu tiên cao' : 'Theo dõi' }}
                                    </span>
                                </div>

                                <div class="fw-bold mt-3 text-danger">{{ number_format((float) $hoaDon->so_tien_con_lai, 0, ',', '.') }} VNĐ</div>
                                <div class="table-subtext mt-1">
                                    Đã thu {{ number_format((float) $hoaDon->so_tien_da_thu, 0, ',', '.') }} VNĐ
                                    • Hạn theo dõi {{ optional($hoaDon->ngay_den_han_thu)->format('d/m/Y') ?? '-' }}
                                </div>
                            </a>
                        @empty
                            <div class="empty-state">
                                Hiện chưa có hóa đơn cần ưu tiên xử lý.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tóm tắt trạng thái</h5>

                    @foreach($trangThaiTongHop as $item)
                        <div class="metric-mini">
                            <div>
                                <div class="fw-semibold">{{ $item['label'] }}</div>
                                <div class="table-subtext">{{ number_format(($item['value'] / $tongHoaDonCoTrangThai) * 100, 1, ',', '.') }}% danh sách</div>
                            </div>
                            <div class="text-end">
                                <strong>{{ $item['value'] }}</strong>
                                <div class="mt-1"><span class="{{ $item['class'] }}">{{ $item['label'] }}</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
