@extends('layouts.admin')

@section('title', 'Bảng điều khiển quản trị')

@push('styles')
    <style>
        .admin-hero-panel {
            border: 1px solid #c7d6e6;
            border-radius: 22px;
            background:
                linear-gradient(135deg, rgba(7, 27, 58, 0.96), rgba(15, 118, 110, 0.9)),
                radial-gradient(circle at 12% 10%, rgba(216, 168, 79, 0.28), transparent 34%);
            color: #fff;
            padding: clamp(22px, 2.2vw, 34px);
            box-shadow: 0 20px 42px rgba(7, 27, 58, 0.18);
        }

        .admin-hero-panel .section-title,
        .admin-hero-panel .section-subtitle {
            color: #fff !important;
        }

        .admin-hero-panel .section-subtitle {
            opacity: 0.84;
        }

        .admin-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .admin-hero-actions .btn {
            min-height: 42px;
            border-radius: 12px;
            font-weight: 800;
        }

        .ops-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .ops-kpi-card {
            border: 1px solid #d4e0ec;
            border-radius: 18px;
            background: #fff;
            padding: 18px;
            min-height: 136px;
            box-shadow: 0 10px 24px rgba(15, 41, 68, 0.06);
        }

        .ops-kpi-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .ops-kpi-icon {
            width: 40px;
            height: 40px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            color: #0f5f58;
            background: #eef8f6;
        }

        .ops-kpi-label {
            color: #405a76;
            font-size: 0.84rem;
            font-weight: 800;
        }

        .ops-kpi-value {
            color: #0e1f35;
            font-size: 1.9rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .ops-kpi-note {
            margin-top: 8px;
            color: #405a76;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .ops-kpi-card.is-warning .ops-kpi-icon {
            color: #9a3412;
            background: #fff7ed;
        }

        .ops-kpi-card.is-success .ops-kpi-icon {
            color: #166534;
            background: #dcfce7;
        }

        .ops-action-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .ops-action-card,
        .recent-booking-card {
            border: 1px solid #d7e3ef;
            border-radius: 16px;
            background: #fff;
            padding: 14px;
            height: 100%;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .ops-action-card:hover,
        .recent-booking-card:hover {
            border-color: #a9c1d8;
            box-shadow: 0 14px 28px rgba(15, 41, 68, 0.09);
            transform: translateY(-2px);
        }

        .ops-action-card strong {
            display: block;
            color: #0e1f35;
            margin-bottom: 4px;
        }

        .ops-action-card i {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            color: #0f5f58;
            background: #eef8f6;
            margin-bottom: 10px;
        }

        @media (max-width: 1199px) {
            .ops-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991px) {
            .admin-hero-actions {
                justify-content: flex-start;
            }

            .ops-action-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .ops-kpi-grid,
            .ops-action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-hero-panel">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h2 class="section-title">Bảng điều khiển quản trị</h2>
                <p class="section-subtitle">Tập trung các việc cần xử lý hôm nay: đặt phòng, thanh toán, tài khoản và doanh thu.</p>
            </div>
            <div class="admin-hero-actions">
                <a href="{{ route('dat-phong.index', ['trang_thai' => 'cho_xac_nhan']) }}" class="btn btn-light">
                    <i class="fa-solid fa-calendar-check me-1"></i>Đơn chờ xác nhận
                </a>
                <a href="{{ route('thanh-toan.index', ['trang_thai' => 'cho_xu_ly', 'nguon_tao' => 'khach_hang']) }}" class="btn btn-warning">
                    <i class="fa-solid fa-receipt me-1"></i>Thanh toán chờ duyệt
                </a>
            </div>
        </div>
    </div>

    <div class="ops-kpi-grid">
        <div class="ops-kpi-card">
            <div class="ops-kpi-top">
                <div>
                    <div class="ops-kpi-label">Tổng người dùng</div>
                    <div class="ops-kpi-value">{{ $tongNguoiDung }}</div>
                </div>
                <div class="ops-kpi-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="ops-kpi-note">{{ $tongTaiKhoanHoatDong }} tài khoản đang hoạt động</div>
        </div>
        <div class="ops-kpi-card">
            <div class="ops-kpi-top">
                <div>
                    <div class="ops-kpi-label">Tổng đơn đặt phòng</div>
                    <div class="ops-kpi-value">{{ $tongDatPhong }}</div>
                </div>
                <div class="ops-kpi-icon"><i class="fa-solid fa-calendar-days"></i></div>
            </div>
            <div class="ops-kpi-note">Đơn từ website: {{ $tongDatPhongOnline }}</div>
        </div>
        <div class="ops-kpi-card is-warning">
            <div class="ops-kpi-top">
                <div>
                    <div class="ops-kpi-label">Đơn chờ xác nhận</div>
                    <div class="ops-kpi-value text-warning">{{ $tongDatPhongChoXacNhan }}</div>
                </div>
                <div class="ops-kpi-icon"><i class="fa-solid fa-clock"></i></div>
            </div>
            <div class="ops-kpi-note">Cần xử lý nhanh để tăng tỷ lệ chuyển đổi.</div>
        </div>
        <div class="ops-kpi-card is-success">
            <div class="ops-kpi-top">
                <div>
                    <div class="ops-kpi-label">Doanh thu tháng này</div>
                    <div class="ops-kpi-value text-success">{{ number_format((float) $doanhThuThangNay, 0, ',', '.') }}</div>
                </div>
                <div class="ops-kpi-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>
            <div class="ops-kpi-note">VNĐ</div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Thanh toán khách đang chờ duyệt</h5>
                    <p class="text-muted small mb-0">Các yêu cầu khách hàng đã gửi thanh toán nhưng còn chờ bộ phận nội bộ xác nhận.</p>
                </div>
                <a
                    href="{{ route('thanh-toan.index', ['trang_thai' => 'cho_xu_ly', 'nguon_tao' => 'khach_hang']) }}"
                    class="btn btn-soft btn-sm"
                >
                    Mở hàng đợi thanh toán
                </a>
            </div>

            <div class="row g-3 align-items-stretch">
                <div class="col-md-4 col-xl-3">
                    <div class="metric-card h-100">
                        <div class="metric-label">Yêu cầu chờ duyệt</div>
                        <div class="metric-value text-warning">{{ $tongYeuCauThanhToanChoXuLy }}</div>
                        <div class="small text-muted mt-2">Cần duyệt hoặc từ chối để chốt công nợ.</div>
                    </div>
                </div>

                <div class="col-md-8 col-xl-9">
                    @if($danhSachThanhToanChoXuLy->isEmpty())
                        <div class="alert alert-success mb-0">Hiện tại không có yêu cầu thanh toán nào từ khách hàng đang chờ xác nhận.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Giao dịch</th>
                                        <th>Khách hàng</th>
                                        <th>Hóa đơn</th>
                                        <th>Số tiền</th>
                                        <th>Thời điểm gửi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($danhSachThanhToanChoXuLy as $thanhToan)
                                        <tr>
                                            <td class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</td>
                                            <td>{{ $thanhToan->hoaDon?->datPhong?->khachHang?->ho_ten ?? '-' }}</td>
                                            <td>{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }}</td>
                                            <td class="fw-semibold text-danger">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Điều hướng nhanh theo nghiệp vụ</h5>
                    <p class="text-muted mb-4">Mở nhanh các chức năng chính để xử lý vận hành hàng ngày.</p>

                    <div class="ops-action-grid">
                        <a href="{{ route('dat-phong.index') }}" class="ops-action-card">
                                <i class="fa-solid fa-calendar-check"></i>
                                <strong>Quản lý đặt phòng</strong>
                                <div class="small text-muted">Duyệt đơn mới, cập nhật trạng thái lưu trú.</div>
                        </a>
                        <a href="{{ route('phong.index') }}" class="ops-action-card">
                                <i class="fa-solid fa-bed"></i>
                                <strong>Quản lý phòng</strong>
                                <div class="small text-muted">Theo dõi tồn kho phòng và giá phòng mặc định.</div>
                        </a>
                        <a href="{{ route('khach-hang.index') }}" class="ops-action-card">
                                <i class="fa-solid fa-users"></i>
                                <strong>Quản lý khách hàng</strong>
                                <div class="small text-muted">Xem hồ sơ, lịch sử và hạng khách hàng.</div>
                        </a>
                        <a href="{{ route('hoa-don.index') }}" class="ops-action-card">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                                <strong>Quản lý hóa đơn</strong>
                                <div class="small text-muted">Kiểm soát công nợ và trạng thái thanh toán.</div>
                        </a>
                        <a href="{{ route('thanh-toan.index', ['trang_thai' => 'cho_xu_ly', 'nguon_tao' => 'khach_hang']) }}" class="ops-action-card">
                                <i class="fa-solid fa-credit-card"></i>
                                <strong>Quản lý thanh toán</strong>
                                <div class="small text-muted">
                                    Ghi nhận giao dịch và đối soát hóa đơn.
                                    @if($tongYeuCauThanhToanChoXuLy > 0)
                                        Hiện có {{ $tongYeuCauThanhToanChoXuLy }} yêu cầu khách đang chờ duyệt.
                                    @endif
                                </div>
                        </a>
                        <a href="{{ route('bao-cao.index') }}" class="ops-action-card">
                                <i class="fa-solid fa-chart-line"></i>
                                <strong>Báo cáo thống kê</strong>
                                <div class="small text-muted">Theo dõi KPI doanh thu và công suất phòng.</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Đơn đặt phòng gần đây</h5>

                    @forelse($datPhongGanDay as $datPhong)
                        @php
                            $phong = $datPhong->chiTietDatPhong->first()?->phong;
                            $mapTrangThai = [
                                'cho_xac_nhan' => 'chip chip-warning',
                                'da_xac_nhan' => 'chip chip-info',
                                'da_nhan_phong' => 'chip chip-neutral',
                                'khong_den' => 'chip chip-danger',
                                'da_tra_phong' => 'chip chip-success',
                                'da_huy' => 'chip chip-danger',
                            ];
                            $chip = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
                        @endphp

                        <div class="recent-booking-card mb-2">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <strong>{{ $datPhong->ma_dat_phong }}</strong>
                                <span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                {{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}
                                @if($phong)
                                    | Phòng {{ $phong->so_phong }}
                                @endif
                            </div>
                            <div class="small text-muted">
                                {{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') }}
                                -
                                {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Chưa có đơn đặt phòng gần đây.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Người dùng mới tạo gần đây</h5>

            <div class="row g-3">
                @forelse($nguoiDungMoi as $item)
                    <div class="col-md-6 col-xl-4">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="fw-bold">{{ $item->ho_ten }}</div>
                            <div class="text-muted small mb-2">{{ $item->email }}</div>
                            @if($item->vai_tro === 'admin')
                                <span class="badge-role-admin">Admin</span>
                            @else
                                <span class="badge-role-nhan-vien">Nhân viên</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">Chưa có dữ liệu người dùng mới.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
