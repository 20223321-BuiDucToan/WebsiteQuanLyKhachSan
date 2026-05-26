@extends('layouts.admin')

@section('title', 'Quản lý dịch vụ')

@push('styles')
    <style>
        .service-hero {
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
        }

        .service-hero .section-title,
        .service-hero .section-subtitle {
            color: inherit;
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

        .service-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }
    </style>
@endpush

@section('content')
    <div class="premium-card service-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quản lý dịch vụ</h2>
                    <p class="section-subtitle">Xây dựng danh mục dịch vụ phục vụ vận hành và đồng bộ doanh thu vào hóa đơn.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dich-vu.create') }}" class="btn btn-gradient">
                        <i class="fa-solid fa-plus me-2"></i>Thêm dịch vụ
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng dịch vụ</div>
                    <div class="hero-stat-value">{{ $thongKe['tong'] }}</div>
                    <div class="hero-stat-note">Toàn bộ dịch vụ đang có trong hệ thống</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Hoạt động</div>
                    <div class="hero-stat-value">{{ $thongKe['hoat_dong'] }}</div>
                    <div class="hero-stat-note">Có thể chọn để ghi nhận vào đơn đặt phòng</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tạm ngưng</div>
                    <div class="hero-stat-value">{{ $thongKe['tam_ngung'] }}</div>
                    <div class="hero-stat-note">Tạm khóa để tránh tiếp tục bán nhầm</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Đã phát sinh</div>
                    <div class="hero-stat-value">{{ $thongKe['da_phat_sinh'] }}</div>
                    <div class="hero-stat-note">Đã có lịch sử sử dụng thực tế trong vận hành</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-xl-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã dịch vụ, tên dịch vụ, loại dịch vụ">
                </div>

                <div class="col-xl-3">
                    <label class="form-label">Loại dịch vụ</label>
                    <select name="loai_dich_vu" class="form-select">
                        <option value="">Tất cả</option>
                        @foreach($danhSachLoaiDichVu as $loai)
                            <option value="{{ $loai }}" @selected($loaiDichVu === $loai)>{{ $loai }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" @selected($trangThai === 'hoat_dong')>Hoạt động</option>
                        <option value="tam_ngung" @selected($trangThai === 'tam_ngung')>Tạm ngưng</option>
                    </select>
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <a href="{{ route('dich-vu.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Danh sách dịch vụ</h5>
                    <div class="text-muted small">{{ $danhSachDichVu->total() }} dịch vụ phù hợp với bộ lọc hiện tại</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Dịch vụ</th>
                            <th>Loại</th>
                            <th>Giá bán</th>
                            <th>Trạng thái</th>
                            <th>Phát sinh</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachDichVu as $dichVu)
                            @php
                                $chipTrangThai = $dichVu->trang_thai === 'hoat_dong' ? 'chip chip-success' : 'chip chip-neutral';
                            @endphp
                            <tr>
                                <td style="min-width: 220px;">
                                    <div class="service-name">{{ $dichVu->ten_dich_vu }}</div>
                                    <div class="table-subtext">{{ $dichVu->ma_dich_vu }} • {{ $dichVu->don_vi_tinh }}</div>
                                </td>
                                <td>{{ $dichVu->loai_dich_vu ?: '-' }}</td>
                                <td class="fw-semibold">{{ number_format((float) $dichVu->don_gia, 0, ',', '.') }} VNĐ / {{ $dichVu->don_vi_tinh }}</td>
                                <td><span class="{{ $chipTrangThai }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($dichVu->trang_thai) }}</span></td>
                                <td>{{ $dichVu->su_dung_dich_vu_count }} lần</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('dich-vu.edit', $dichVu) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                        <form method="POST" action="{{ route('dich-vu.destroy', $dichVu) }}" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có dịch vụ nào trong danh mục.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachDichVu->links() }}</div>
        </div>
    </div>
@endsection
