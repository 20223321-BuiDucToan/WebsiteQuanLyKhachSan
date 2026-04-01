@extends('layouts.admin')

@section('title', 'Bảng điều khiển nhân viên')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Bảng điều khiển nhân viên</h2>
        <p class="section-subtitle">Theo dõi đơn đặt phòng mới và xử lý nghiệp vụ hằng ngày nhanh hơn.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đơn chờ xác nhận</div>
                <div class="metric-value text-warning">{{ $tongDatPhongChoXacNhan }}</div>
                <div class="small text-muted mt-2">Ưu tiên liên hệ khách sớm.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Đơn tạo trong hôm nay</div>
                <div class="metric-value text-primary">{{ $tongDatPhongHomNay }}</div>
                <div class="small text-muted mt-2">Gồm đơn online và tại quầy.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Khách hàng trong hệ thống</div>
                <div class="metric-value text-info">{{ $tongKhachHang }}</div>
                <div class="small text-muted mt-2">Đang được quản lý tập trung.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Hóa đơn cần thu</div>
                <div class="metric-value text-danger">{{ $tongHoaDonCanThu }}</div>
                <div class="small text-muted mt-2">Thu hôm nay: {{ number_format((float) $tongTienThuHomNay, 0, ',', '.') }} VNĐ</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="premium-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="fw-bold mb-0">Danh sách đơn cần xử lý</h5>
                        <a href="{{ route('dat-phong.index') }}" class="btn btn-soft btn-sm">Mở quản lý đặt phòng</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Phòng</th>
                                    <th>Lịch ở</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($danhSachCanXuLy as $datPhong)
                                    @php
                                        $phong = $datPhong->chiTietDatPhong->first()?->phong;
                                        $chip = $datPhong->trang_thai === 'cho_xac_nhan' ? 'chip chip-warning' : 'chip chip-info';
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $datPhong->ma_dat_phong }}</td>
                                        <td>
                                            <div>{{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}</div>
                                            <div class="small text-muted">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }}</div>
                                        </td>
                                        <td>{{ $phong ? 'Phòng ' . $phong->so_phong : '-' }}</td>
                                        <td>
                                            {{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') }}
                                            -
                                            {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Hiện tại không có đơn cần xử lý.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin tài khoản</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Họ tên</div>
                        <div class="fw-semibold">{{ $nguoiDung->ho_ten }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ $nguoiDung->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Tên đăng nhập</div>
                        <div class="fw-semibold">{{ $nguoiDung->ten_dang_nhap }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Vai trò</div>
                        <span class="badge-role-nhan-vien">Nhân viên</span>
                    </div>
                    <div>
                        <div class="text-muted small">Lần đăng nhập cuối</div>
                        <div class="fw-semibold">
                            {{ $nguoiDung->lan_dang_nhap_cuoi ? $nguoiDung->lan_dang_nhap_cuoi->format('d/m/Y H:i') : 'Chưa có dữ liệu' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tác vụ nhanh</h5>

                    <div class="d-grid gap-2">
                        <a href="{{ route('khach-hang.index') }}" class="btn btn-outline-secondary">Quản lý khách hàng</a>
                        <a href="{{ route('thanh-toan.index') }}" class="btn btn-outline-success">Quản lý thanh toán</a>
                        <a href="{{ route('hoa-don.index') }}" class="btn btn-outline-primary">Quản lý hóa đơn</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
