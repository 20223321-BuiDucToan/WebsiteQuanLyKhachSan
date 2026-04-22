@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')

@push('styles')
    <style>
        .customer-hero {
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
        }

        .customer-hero .section-title,
        .customer-hero .section-subtitle {
            color: inherit;
        }

        .customer-hero .section-subtitle {
            opacity: 1;
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
            background: #f8fbff;
            border: 1px solid #dbe7f2;
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b8298;
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
            color: #6b8298;
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

        .customer-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .activity-track {
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: #e7edf4;
        }

        .activity-track > span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0f766e, #22c55e);
        }

        .highlight-list {
            display: grid;
            gap: 12px;
        }

        .highlight-item {
            display: block;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid #e3ebf3;
            background: #fbfdff;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .highlight-item:hover {
            transform: translateY(-2px);
            border-color: #c7dae9;
            box-shadow: 0 12px 24px rgba(15, 41, 68, 0.08);
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
            font-size: 1.08rem;
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
            ['label' => 'Khách hoạt động', 'query' => array_filter(array_merge(request()->only('tu_khoa'), ['trang_thai' => 'hoat_dong']))],
            ['label' => 'Khách tạm khóa', 'query' => array_filter(array_merge(request()->only('tu_khoa'), ['trang_thai' => 'tam_khoa']))],
            ['label' => 'Hạng vàng', 'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), ['hang_khach_hang' => 'vang']))],
            ['label' => 'Kim cương', 'query' => array_filter(array_merge(request()->only('tu_khoa', 'trang_thai'), ['hang_khach_hang' => 'kim_cuong']))],
        ];

        $coBoLoc = filled($tuKhoa) || filled($hangKhachHang) || filled($trangThai);

        $tongKhach = max(1, $thongKe['tong_hien_thi']);

        $tongHopChamSoc = [
            ['label' => 'Đang hoạt động', 'value' => $thongKe['hoat_dong'], 'class' => 'chip chip-success'],
            ['label' => 'Tạm khóa', 'value' => $thongKe['tam_khoa'], 'class' => 'chip chip-warning'],
            ['label' => 'VIP', 'value' => $thongKe['vip'], 'class' => 'chip chip-info'],
            ['label' => 'Có liên hệ', 'value' => $thongKe['co_lien_he'], 'class' => 'chip chip-neutral'],
        ];

        $mauHang = [
            'kim_cuong' => 'chip chip-info',
            'vang' => 'chip chip-warning',
            'bac' => 'chip chip-neutral',
            'thuong' => 'chip chip-success',
        ];

        $nhanNhom = [
            'trung_thanh' => ['label' => 'Trung thành', 'class' => 'chip chip-info'],
            'tiem_nang' => ['label' => 'Tiềm năng', 'class' => 'chip chip-warning'],
            'moi' => ['label' => 'Mới', 'class' => 'chip chip-neutral'],
        ];
    @endphp

    <div class="premium-card customer-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quản lý khách hàng</h2>
                    <p class="section-subtitle">Theo dõi hồ sơ, giá trị giao dịch, mức độ quay lại và nhóm khách cần ưu tiên chăm sóc.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-calendar-check me-2"></i>Xem đặt phòng
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Khách đang hiển thị</div>
                    <div class="hero-stat-value">{{ $thongKe['tong_hien_thi'] }}</div>
                    <div class="hero-stat-note">Toàn hệ thống: {{ $thongKe['tong_toan_he_thong'] }}</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Khách hoạt động</div>
                    <div class="hero-stat-value">{{ $thongKe['hoat_dong'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['vip'] }} khách thuộc nhóm VIP</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Khách quay lại</div>
                    <div class="hero-stat-value">{{ $thongKe['quay_lai'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['tong_luot_dat'] }} lượt đặt trong bộ lọc hiện tại</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Doanh thu theo khách</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKe['tong_doanh_thu'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">{{ $thongKe['co_lien_he'] }} hồ sơ có đủ kênh liên hệ cơ bản</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <div class="d-none">
                @foreach($quickFilters as $filter)
                    <a href="{{ route('khach-hang.index', $filter['query']) }}" class="quick-filter">
                        <i class="fa-solid fa-filter"></i>{{ $filter['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="GET" class="row g-3">
                <div class="col-xl-6">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã KH, tên, số điện thoại, email, giấy tờ">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Hạng khách hàng</label>
                    <select name="hang_khach_hang" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="thuong" @selected($hangKhachHang === 'thuong')>Thường</option>
                        <option value="bac" @selected($hangKhachHang === 'bac')>Bạc</option>
                        <option value="vang" @selected($hangKhachHang === 'vang')>Vàng</option>
                        <option value="kim_cuong" @selected($hangKhachHang === 'kim_cuong')>Kim cương</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" @selected($trangThai === 'hoat_dong')>Hoạt động</option>
                        <option value="tam_khoa" @selected($trangThai === 'tam_khoa')>Tạm khóa</option>
                    </select>
                </div>

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-xl-1 col-md-6 d-flex align-items-end">
                    <a href="{{ route('khach-hang.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>

            @if($coBoLoc)
                <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                    @if($tuKhoa)
                        <span class="active-filter-chip"><i class="fa-solid fa-magnifying-glass"></i>{{ $tuKhoa }}</span>
                    @endif
                    @if($hangKhachHang)
                        <span class="active-filter-chip"><i class="fa-solid fa-crown"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($hangKhachHang) }}</span>
                    @endif
                    @if($trangThai)
                        <span class="active-filter-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($trangThai) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Danh sách khách hàng</h5>
                            <div class="text-muted small">
                                {{ $thongKe['tong_hien_thi'] }} khách trong bộ lọc hiện tại
                                • {{ $thongKe['tong_luot_dat'] }} lượt đặt
                                • {{ number_format((float) $thongKe['tong_doanh_thu'], 0, ',', '.') }} VNĐ
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Khách quay lại</div>
                            <div class="fw-bold">{{ number_format(($thongKe['quay_lai'] / $tongKhach) * 100, 1, ',', '.') }}%</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Liên hệ</th>
                                    <th>Đặt phòng và doanh thu</th>
                                    <th>Thành viên</th>
                                    <th>Trạng thái</th>
                                    <th>Hoạt động gần nhất</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachKhachHang as $khachHang)
                                    @php
                                        $nhom = $nhanNhom[$khachHang->nhom_khach_hang_hien_thi] ?? $nhanNhom['moi'];
                                        $lanDatPhongGanNhat = $khachHang->datPhong->first()?->ngay_dat;
                                    @endphp

                                    <tr>
                                        <td style="min-width: 220px;">
                                            <div class="customer-name">{{ $khachHang->ho_ten }}</div>
                                            <div class="table-subtext">{{ $khachHang->ma_khach_hang }}</div>
                                            <div class="table-subtext">{{ $khachHang->quoc_tich ?: 'Chưa cập nhật quốc tịch' }}</div>
                                        </td>
                                        <td style="min-width: 190px;">
                                            <div>{{ $khachHang->so_dien_thoai ?: '-' }}</div>
                                            <div class="table-subtext">{{ $khachHang->email ?: 'Chưa có email' }}</div>
                                            <div class="table-subtext">{{ $khachHang->so_giay_to ?: 'Chưa có giấy tờ' }}</div>
                                        </td>
                                        <td style="min-width: 260px;">
                                            <div class="fw-semibold">{{ $khachHang->dat_phong_count }} lượt đặt • {{ $khachHang->dat_phong_hoan_tat_count }} hoàn tất</div>
                                            <div class="table-subtext mb-2">
                                                Hủy {{ $khachHang->dat_phong_da_huy_count }}
                                                • Doanh thu {{ number_format((float) $khachHang->tong_doanh_thu, 0, ',', '.') }} VNĐ
                                            </div>
                                            <div class="activity-track">
                                                <span style="width: {{ min(100, max(0, $khachHang->ty_le_hoan_tat)) }}%"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-2">
                                                <span class="{{ $mauHang[$khachHang->hang_khach_hang] ?? 'chip chip-neutral' }}">
                                                    {{ \App\Support\HienThiGiaTri::nhanGiaTri($khachHang->hang_khach_hang) }}
                                                </span>
                                            </div>
                                            <span class="{{ $nhom['class'] }}">{{ $nhom['label'] }}</span>
                                        </td>
                                        <td>
                                            <div class="mb-2">
                                                @if($khachHang->trang_thai === 'hoat_dong')
                                                    <span class="chip chip-success">Hoạt động</span>
                                                @else
                                                    <span class="chip chip-warning">Tạm khóa</span>
                                                @endif
                                            </div>
                                            @if($khachHang->co_thong_tin_lien_he)
                                                <span class="chip chip-neutral">Có thể liên hệ</span>
                                            @else
                                                <span class="chip chip-danger">Thiếu liên hệ</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lanDatPhongGanNhat)
                                                <div class="fw-semibold">{{ optional($lanDatPhongGanNhat)->format('d/m/Y H:i') }}</div>
                                                <div class="table-subtext">Tương tác gần nhất</div>
                                            @else
                                                <div class="fw-semibold">Chưa có đặt phòng</div>
                                                <div class="table-subtext">Cần nuôi dưỡng</div>
                                            @endif
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;">
                                            <a href="{{ route('khach-hang.show', $khachHang) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>Xem
                                            </a>
                                            <a href="{{ route('khach-hang.edit', $khachHang) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <form action="{{ route('khach-hang.doi-trang-thai', $khachHang) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Đổi trạng thái khách hàng này?')">
                                                    <i class="fa-solid fa-lock"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4">
                                            <div class="empty-state">
                                                Chưa có khách hàng nào theo bộ lọc hiện tại.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $danhSachKhachHang->links() }}</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 d-none">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Khách nổi bật</h5>
                            <p class="text-muted small mb-0">Ưu tiên nhóm có nhiều lượt đặt hoặc doanh thu cao.</p>
                        </div>
                        <span class="chip chip-info">{{ $khachHangNoiBat->count() }} hồ sơ</span>
                    </div>

                    <div class="highlight-list">
                        @forelse($khachHangNoiBat as $khachHang)
                            @php
                                $nhom = $nhanNhom[$khachHang->nhom_khach_hang_hien_thi] ?? $nhanNhom['moi'];
                            @endphp
                            <a href="{{ route('khach-hang.show', $khachHang) }}" class="highlight-item">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-bold">{{ $khachHang->ho_ten }}</div>
                                        <div class="table-subtext">{{ $khachHang->ma_khach_hang }}</div>
                                    </div>
                                    <span class="{{ $nhom['class'] }}">{{ $nhom['label'] }}</span>
                                </div>

                                <div class="fw-bold mt-3">{{ number_format((float) $khachHang->tong_doanh_thu, 0, ',', '.') }} VNĐ</div>
                                <div class="table-subtext mt-1">{{ $khachHang->dat_phong_count }} lượt đặt • {{ $khachHang->dat_phong_hoan_tat_count }} hoàn tất</div>
                            </a>
                        @empty
                            <div class="empty-state">
                                Chưa có khách nổi bật trong bộ lọc hiện tại.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tổng quan chăm sóc</h5>

                    @foreach($tongHopChamSoc as $item)
                        <div class="metric-mini">
                            <div>
                                <div class="fw-semibold">{{ $item['label'] }}</div>
                                <div class="table-subtext">{{ number_format(($item['value'] / $tongKhach) * 100, 1, ',', '.') }}% danh sách</div>
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
