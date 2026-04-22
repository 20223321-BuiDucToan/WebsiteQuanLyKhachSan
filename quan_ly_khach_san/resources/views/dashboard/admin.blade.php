@extends('layouts.admin')

@section('title', 'Bảng điều khiển quản trị')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Bảng điều khiển quản trị</h2>
        <p class="section-subtitle">Toàn bộ số liệu vận hành được đồng bộ từ đặt phòng online và dữ liệu nội bộ.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Tổng người dùng</div>
                <div class="metric-value">{{ $tongNguoiDung }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Tài khoản quản trị</div>
                <div class="metric-value">{{ $tongAdmin }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Tài khoản nhân viên</div>
                <div class="metric-value">{{ $tongNhanVien }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Tài khoản hoạt động</div>
                <div class="metric-value text-success">{{ $tongTaiKhoanHoatDong }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Tổng đơn đặt phòng</div>
                <div class="metric-value">{{ $tongDatPhong }}</div>
                <div class="small text-muted mt-2">Đơn từ website: {{ $tongDatPhongOnline }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Đơn chờ xác nhận</div>
                <div class="metric-value text-warning">{{ $tongDatPhongChoXacNhan }}</div>
                <div class="small text-muted mt-2">Cần xử lý nhanh để tăng tỷ lệ chuyển đổi.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Doanh thu tháng này</div>
                <div class="metric-value text-success">{{ number_format((float) $doanhThuThangNay, 0, ',', '.') }}</div>
                <div class="small text-muted mt-2">VNĐ</div>
            </div>
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

                    <div class="row g-3">
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('dat-phong.index') }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Quản lý đặt phòng</div>
                                <div class="small text-muted">Duyệt đơn mới, cập nhật trạng thái lưu trú.</div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('phong.index') }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-bed me-2 text-info"></i>Quản lý phòng</div>
                                <div class="small text-muted">Theo dõi tồn kho phòng và giá phòng mặc định.</div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('khach-hang.index') }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-users me-2 text-secondary"></i>Quản lý khách hàng</div>
                                <div class="small text-muted">Xem hồ sơ, lịch sử và hạng khách hàng.</div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('hoa-don.index') }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-file-invoice-dollar me-2 text-danger"></i>Quản lý hóa đơn</div>
                                <div class="small text-muted">Kiểm soát công nợ và trạng thái thanh toán.</div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('thanh-toan.index', ['trang_thai' => 'cho_xu_ly', 'nguon_tao' => 'khach_hang']) }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-credit-card me-2 text-success"></i>Quản lý thanh toán</div>
                                <div class="small text-muted">
                                    Ghi nhận giao dịch và đối soát hóa đơn.
                                    @if($tongYeuCauThanhToanChoXuLy > 0)
                                        Hiện có {{ $tongYeuCauThanhToanChoXuLy }} yêu cầu khách đang chờ duyệt.
                                    @endif
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route('bao-cao.index') }}" class="d-block border rounded-4 p-3 h-100">
                                <div class="fw-bold mb-1"><i class="fa-solid fa-chart-line me-2 text-warning"></i>Báo cáo thống kê</div>
                                <div class="small text-muted">Theo dõi KPI doanh thu và công suất phòng.</div>
                            </a>
                        </div>
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
                                'da_tra_phong' => 'chip chip-success',
                                'da_huy' => 'chip chip-danger',
                            ];
                            $chip = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
                        @endphp

                        <div class="border rounded-4 p-3 mb-2">
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
