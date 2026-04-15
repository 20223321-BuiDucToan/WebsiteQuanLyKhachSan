@extends('layouts.admin')

@section('title', 'Chi tiết đặt phòng')

@push('styles')
    <style>
        .booking-detail-hero {
            border: none;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.22), transparent 34%),
                linear-gradient(135deg, #173652, #0f766e 58%, #14b8a6);
            color: #fff;
        }

        .booking-detail-hero .section-title,
        .booking-detail-hero .section-subtitle {
            color: #fff;
        }

        .booking-detail-hero .section-subtitle {
            opacity: 0.84;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #fff;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .hero-stat-card {
            border-radius: 18px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.84;
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.45rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            opacity: 0.78;
        }

        .info-grid {
            display: grid;
            gap: 16px;
        }

        .info-block {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid #e5edf6;
            background: #fbfdff;
        }

        .info-label {
            color: #68839f;
            font-size: 0.82rem;
            margin-bottom: 4px;
        }

        .info-value {
            color: #173652;
            font-weight: 700;
        }

        .timeline-list {
            display: grid;
            gap: 16px;
        }

        .timeline-item {
            display: flex;
            gap: 12px;
        }

        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            margin-top: 4px;
            background: #0f766e;
            box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.12);
            flex-shrink: 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f8;
        }

        .summary-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
    @php
        $mapTrangThai = [
            'cho_xac_nhan' => 'chip chip-warning',
            'da_xac_nhan' => 'chip chip-info',
            'da_nhan_phong' => 'chip chip-neutral',
            'da_tra_phong' => 'chip chip-success',
            'da_huy' => 'chip chip-danger',
        ];

        $chipTrangThai = $mapTrangThai[$datPhong->trang_thai] ?? 'chip chip-neutral';
        $chipUuTien = $datPhong->muc_do_uu_tien === 'cao'
            ? 'chip chip-danger'
            : ($datPhong->muc_do_uu_tien === 'trung_binh' ? 'chip chip-warning' : 'chip chip-neutral');
    @endphp

    <div class="premium-card booking-detail-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Chi tiết đơn {{ $datPhong->ma_dat_phong }}</h2>
                    <p class="section-subtitle">
                        Tạo lúc {{ optional($datPhong->ngay_dat)->format('d/m/Y H:i') ?? '-' }}
                        @if($datPhong->nguoiTao)
                            • Người tạo: {{ $datPhong->nguoiTao->ho_ten }}
                        @endif
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="hero-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                    <span class="hero-chip"><i class="fa-solid fa-bolt"></i>{{ ucfirst($datPhong->muc_do_uu_tien) }}</span>
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-list me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng tiền tạm tính</div>
                    <div class="hero-stat-value">{{ number_format((float) $tongTienPhong, 0, ',', '.') }}</div>
                    <div class="hero-stat-note">VNĐ tiền phòng trong đơn hiện tại</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Lưu trú</div>
                    <div class="hero-stat-value">{{ $datPhong->tong_so_dem }} đêm</div>
                    <div class="hero-stat-note">{{ $datPhong->tong_so_phong }} phòng • {{ (int) $datPhong->so_nguoi_lon + (int) $datPhong->so_tre_em }} khách</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Hóa đơn</div>
                    <div class="hero-stat-value">{{ $hoaDonHienTai ? $hoaDonHienTai->ma_hoa_don : 'Chưa có' }}</div>
                    <div class="hero-stat-note">{{ $hoaDonHienTai ? number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') . ' VNĐ còn lại' : 'Sẽ tạo tự động khi trả phòng' }}</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Vận hành</div>
                    <div class="hero-stat-value">{{ ucfirst($datPhong->muc_do_uu_tien) }}</div>
                    <div class="hero-stat-note">{{ $datPhong->ghi_chu_van_hanh }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Thông tin khách hàng</h5>
                            <p class="text-muted small mb-0">Hồ sơ liên hệ chính của khách trong đơn đặt phòng này.</p>
                        </div>
                        @if($datPhong->khachHang)
                            <a href="{{ route('khach-hang.show', $datPhong->khachHang) }}" class="btn btn-sm btn-outline-primary">Xem hồ sơ khách</a>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">Họ tên</div>
                                <div class="info-value">{{ $datPhong->khachHang?->ho_ten ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">Số điện thoại</div>
                                <div class="info-value">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-block">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $datPhong->khachHang?->email ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Danh sách phòng trong đơn</h5>
                            <p class="text-muted small mb-0">Tổng hợp loại phòng, giá đêm và trạng thái từng phòng đã gán.</p>
                        </div>
                        <div class="fw-bold text-primary">{{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Giá / đêm</th>
                                    <th>Số đêm</th>
                                    <th>Thành tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datPhong->chiTietDatPhong as $chiTiet)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $chiTiet->phong?->so_phong ? 'Phòng ' . $chiTiet->phong->so_phong : '-' }}</div>
                                            <div class="table-subtext">Tầng {{ $chiTiet->phong?->tang ?? '-' }}</div>
                                        </td>
                                        <td>{{ $chiTiet->phong?->loaiPhong?->ten_loai_phong ?? '-' }}</td>
                                        <td>{{ number_format((float) $chiTiet->gia_phong, 0, ',', '.') }} VNĐ</td>
                                        <td>{{ $chiTiet->so_dem }}</td>
                                        <td class="fw-semibold">{{ number_format((float) $chiTiet->gia_phong * (int) $chiTiet->so_dem, 0, ',', '.') }} VNĐ</td>
                                        <td><span class="chip chip-neutral">{{ \App\Support\HienThiGiaTri::nhanGiaTri($chiTiet->trang_thai) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Chưa có chi tiết phòng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Yêu cầu và ghi chú</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-block h-100">
                                <div class="info-label">Yêu cầu đặc biệt</div>
                                <div class="info-value">{{ $datPhong->yeu_cau_dac_biet ?: '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-block h-100">
                                <div class="info-label">Ghi chú nội bộ</div>
                                <div class="info-value">{{ $datPhong->ghi_chu ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tổng hợp lưu trú</h5>

                    <div class="summary-row">
                        <span class="text-muted">Trạng thái</span>
                        <span class="{{ $chipTrangThai }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Ưu tiên xử lý</span>
                        <span class="{{ $chipUuTien }}">{{ ucfirst($datPhong->muc_do_uu_tien) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Ngày nhận dự kiến</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Ngày trả dự kiến</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Nhận phòng thực tế</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Trả phòng thực tế</span>
                        <span class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Số người ở</span>
                        <span class="fw-semibold">{{ (int) $datPhong->so_nguoi_lon }} NL • {{ (int) $datPhong->so_tre_em }} TE</span>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Nguồn đặt</span>
                        <span class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? '-') }}</span>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Hóa đơn liên quan</h5>

                    @if($hoaDonHienTai)
                        <div class="summary-row">
                            <span class="text-muted">Mã hóa đơn</span>
                            <span class="fw-semibold">{{ $hoaDonHienTai->ma_hoa_don }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Tổng tiền</span>
                            <span class="fw-semibold">{{ number_format((float) $hoaDonHienTai->tong_tien, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Đã thu</span>
                            <span class="fw-semibold text-success">{{ number_format((float) $datPhong->so_tien_da_thu_hoa_don, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">Còn lại</span>
                            <span class="fw-semibold text-danger">{{ number_format((float) $datPhong->so_tien_con_lai_hoa_don, 0, ',', '.') }} VNĐ</span>
                        </div>

                        <a href="{{ route('hoa-don.show', $hoaDonHienTai) }}" class="btn btn-outline-success w-100 mt-3">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Xem hóa đơn
                        </a>
                    @else
                        <div class="text-muted small">Đơn này chưa có hóa đơn. Hệ thống sẽ tự tạo khi trạng thái chuyển sang đã trả phòng, hoặc bạn có thể tạo thủ công ngay bây giờ.</div>

                        <a href="{{ route('hoa-don.create', ['dat_phong_id' => $datPhong->id]) }}" class="btn btn-outline-success w-100 mt-3">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Tạo hóa đơn cho đơn này
                        </a>
                    @endif
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Cập nhật trạng thái đơn</h5>

                    <form method="POST" action="{{ route('dat-phong.cap-nhat-trang-thai', $datPhong) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Trạng thái hiện tại</label>
                            <select name="trang_thai" class="form-select">
                                <option value="cho_xac_nhan" @selected($datPhong->trang_thai === 'cho_xac_nhan')>Chờ xác nhận</option>
                                <option value="da_xac_nhan" @selected($datPhong->trang_thai === 'da_xac_nhan')>Đã xác nhận</option>
                                <option value="da_nhan_phong" @selected($datPhong->trang_thai === 'da_nhan_phong')>Đã nhận phòng</option>
                                <option value="da_tra_phong" @selected($datPhong->trang_thai === 'da_tra_phong')>Đã trả phòng</option>
                                <option value="da_huy" @selected($datPhong->trang_thai === 'da_huy')>Đã hủy</option>
                            </select>
                        </div>

                        <div class="text-muted small mb-3">{{ $datPhong->ghi_chu_van_hanh }}</div>

                        <button type="submit" class="btn btn-gradient w-100">Lưu trạng thái</button>
                    </form>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Mốc xử lý</h5>

                    <div class="timeline-list">
                        @foreach($timeline as $moc)
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <div class="fw-semibold">{{ $moc['label'] }}</div>
                                        <span class="{{ $moc['class'] }}">{{ $moc['label'] }}</span>
                                    </div>
                                    <div class="text-muted small">
                                        @if($moc['thoi_gian'])
                                            {{ $moc['co_gio'] ? $moc['thoi_gian']->format('d/m/Y H:i') : $moc['thoi_gian']->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="small mt-1">{{ $moc['ghi_chu'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
