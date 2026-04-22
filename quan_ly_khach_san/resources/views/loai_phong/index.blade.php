@extends('layouts.admin')

@section('title', 'Quản lý loại phòng')

@push('styles')
    <style>
        .room-type-hero {
            border: 1px solid #e5edf6;
            background: #fff;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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
            color: #12304d;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #6b8298;
        }

        .room-type-name {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .feature-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            background: #edf7f6;
            color: #0f766e;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 6px;
            margin-bottom: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="premium-card room-type-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quản lý loại phòng</h2>
                    <p class="section-subtitle">Chuẩn hóa hạng phòng, giá chuẩn, sức chứa và cấu hình tiện ích để vận hành nhất quán.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('loai-phong.create') }}" class="btn btn-gradient">
                        <i class="fa-solid fa-layer-group me-2"></i>Thêm loại phòng
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng loại phòng</div>
                    <div class="hero-stat-value">{{ $thongKe['tong'] }}</div>
                    <div class="hero-stat-note">Toàn bộ hạng phòng đang được cấu hình trong hệ thống</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Hoạt động</div>
                    <div class="hero-stat-value">{{ $thongKe['hoat_dong'] }}</div>
                    <div class="hero-stat-note">Có thể dùng để mở bán và gán cho phòng mới</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tạm ngừng</div>
                    <div class="hero-stat-value">{{ $thongKe['tam_ngung'] }}</div>
                    <div class="hero-stat-note">Được giữ lịch sử nhưng tạm khóa khỏi vận hành mới</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Đang sử dụng</div>
                    <div class="hero-stat-value">{{ $thongKe['dang_su_dung'] }}</div>
                    <div class="hero-stat-note">Đã có ít nhất một phòng thực tế đang gắn loại này</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-xl-5">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã loại phòng, tên loại phòng, loại giường">
                </div>

                <div class="col-xl-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="hoat_dong" @selected($trangThai === 'hoat_dong')>Hoạt động</option>
                        <option value="tam_ngung" @selected($trangThai === 'tam_ngung')>Tạm ngừng</option>
                    </select>
                </div>

                <div class="col-xl-2">
                    <label class="form-label">Tối thiểu khách</label>
                    <input type="number" min="1" max="20" name="so_nguoi_toi_da" class="form-control" value="{{ $soNguoiToiDa }}" placeholder="2">
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-xl-1 d-flex align-items-end">
                    <a href="{{ route('loai-phong.index') }}" class="btn btn-soft w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Danh sách loại phòng</h5>
                    <div class="text-muted small">{{ $danhSachLoaiPhong->total() }} loại phòng phù hợp với bộ lọc hiện tại</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Loại phòng</th>
                            <th>Giá chuẩn</th>
                            <th>Sức chứa</th>
                            <th>Cấu hình</th>
                            <th>Tiện ích</th>
                            <th>Phòng đang gán</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachLoaiPhong as $loaiPhong)
                            <tr>
                                <td style="min-width: 250px;">
                                    <div class="room-type-name">{{ $loaiPhong->ten_loai_phong }}</div>
                                    <div class="table-subtext">{{ $loaiPhong->ma_loai_phong }}</div>
                                    @if($loaiPhong->mo_ta)
                                        <div class="table-subtext mt-1">{{ \Illuminate\Support\Str::limit($loaiPhong->mo_ta, 95) }}</div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ number_format((float) $loaiPhong->gia_mot_dem, 0, ',', '.') }} VNĐ / đêm</td>
                                <td>
                                    {{ $loaiPhong->so_nguoi_toi_da }} khách
                                    @if($loaiPhong->dien_tich)
                                        <div class="table-subtext">{{ number_format((float) $loaiPhong->dien_tich, 1, ',', '.') }} m²</div>
                                    @endif
                                </td>
                                <td>
                                    {{ $loaiPhong->so_giuong }} giường
                                    <div class="table-subtext">
                                        {{ $loaiPhong->loai_giuong ?: 'Chưa cấu hình loại giường' }} • {{ $loaiPhong->so_phong_tam }} phòng tắm
                                    </div>
                                </td>
                                <td style="min-width: 220px;">
                                    @if($loaiPhong->co_ban_cong)
                                        <span class="feature-badge">Ban công</span>
                                    @endif
                                    @if($loaiPhong->co_bep_rieng)
                                        <span class="feature-badge">Bếp riêng</span>
                                    @endif
                                    @if($loaiPhong->co_huong_bien)
                                        <span class="feature-badge">Hướng biển</span>
                                    @endif
                                    @if(! $loaiPhong->co_ban_cong && ! $loaiPhong->co_bep_rieng && ! $loaiPhong->co_huong_bien)
                                        <span class="text-muted small">Chưa có tiện ích nổi bật</span>
                                    @endif
                                </td>
                                <td>{{ $loaiPhong->phong_count }}</td>
                                <td>
                                    @if($loaiPhong->trang_thai === 'hoat_dong')
                                        <span class="chip chip-success">Hoạt động</span>
                                    @else
                                        <span class="chip chip-warning">Tạm ngừng</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('loai-phong.edit', $loaiPhong) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                        <form method="POST" action="{{ route('loai-phong.destroy', $loaiPhong) }}" onsubmit="return confirm('Bạn có chắc muốn xóa loại phòng này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Chưa có loại phòng nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachLoaiPhong->links() }}</div>
        </div>
    </div>
@endsection
