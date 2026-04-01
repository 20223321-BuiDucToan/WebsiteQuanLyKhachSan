@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Quản lý khách hàng</h2>
        <p class="section-subtitle">Theo dõi hồ sơ khách, hạng thành viên và tần suất đặt phòng.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Khách hàng đang hiển thị</div>
                <div class="metric-value">{{ $thongKe['tong_hien_thi'] }}</div>
                <div class="small text-muted mt-2">Tổng toàn hệ thống: {{ $thongKe['tong'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Khách đang hoạt động</div>
                <div class="metric-value text-success">{{ $thongKe['hoat_dong'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-label">Khách hạng kim cương</div>
                <div class="metric-value text-primary">{{ $thongKe['kim_cuong'] }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã KH, tên, số điện thoại, email, giấy tờ">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Hạng khách hàng</label>
                    <select name="hang_khach_hang" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="thuong" @selected($hangKhachHang === 'thuong')>Thường</option>
                        <option value="bac" @selected($hangKhachHang === 'bac')>Bạc</option>
                        <option value="vang" @selected($hangKhachHang === 'vang')>Vàng</option>
                        <option value="kim_cuong" @selected($hangKhachHang === 'kim_cuong')>Kim cương</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" @selected($trangThai === 'hoat_dong')>Hoạt động</option>
                        <option value="tam_khoa" @selected($trangThai === 'tam_khoa')>Tạm khóa</option>
                    </select>
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <a href="{{ route('khach-hang.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="small text-muted mb-3">
                Hiển thị {{ $danhSachKhachHang->count() }} khách hàng trên trang này ({{ $danhSachKhachHang->total() }} kết quả theo bộ lọc hiện tại).
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mã KH</th>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th>Hạng</th>
                            <th>Trạng thái</th>
                            <th>Số lượt đặt</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachKhachHang as $khachHang)
                            @php
                                $mauHang = match ($khachHang->hang_khach_hang) {
                                    'kim_cuong' => 'chip chip-info',
                                    'vang' => 'chip chip-warning',
                                    'bac' => 'chip chip-neutral',
                                    default => 'chip chip-success',
                                };
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $khachHang->ma_khach_hang }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $khachHang->ho_ten }}</div>
                                    <div class="small text-muted">{{ $khachHang->quoc_tich ?: 'Chưa cập nhật quốc tịch' }}</div>
                                </td>
                                <td>
                                    <div>{{ $khachHang->so_dien_thoai ?: '-' }}</div>
                                    <div class="small text-muted">{{ $khachHang->email ?: '-' }}</div>
                                </td>
                                <td><span class="{{ $mauHang }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($khachHang->hang_khach_hang) }}</span></td>
                                <td>
                                    @if($khachHang->trang_thai === 'hoat_dong')
                                        <span class="chip chip-success">Hoạt động</span>
                                    @else
                                        <span class="chip chip-warning">Tạm khóa</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $khachHang->dat_phong_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('khach-hang.show', $khachHang) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('khach-hang.edit', $khachHang) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-pen"></i></a>

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
                                <td colspan="7" class="text-center text-muted py-4">Chưa có khách hàng nào theo bộ lọc hiện tại.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachKhachHang->links() }}</div>
        </div>
    </div>
@endsection
