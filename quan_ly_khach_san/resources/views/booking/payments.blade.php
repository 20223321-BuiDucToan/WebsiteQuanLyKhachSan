@extends('layouts.app')

@section('title', 'Thanh toán của tôi')

@push('styles')
    <style>
        .payment-hero {
            border: 1px solid #d8e3ef;
            border-radius: 26px;
            background:
                radial-gradient(circle at top left, #dff5ef 0, transparent 28%),
                linear-gradient(145deg, #ffffff, #f7fbff);
            box-shadow: 0 20px 42px rgba(16, 42, 67, 0.08);
            padding: 28px;
        }

        .payment-stat-grid,
        .deposit-grid,
        .booking-grid {
            display: grid;
            gap: 14px;
        }

        .payment-stat-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 22px;
        }

        .payment-stat-card,
        .payment-panel,
        .deposit-card,
        .booking-card {
            border: 1px solid #dbe6f1;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 16px 34px rgba(16, 42, 67, 0.06);
        }

        .payment-stat-card {
            padding: 16px;
            background: #f8fbff;
        }

        .payment-stat-label {
            color: #68839f;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .payment-stat-value {
            margin-top: 6px;
            font-size: 1.45rem;
            font-weight: 800;
            color: #10243e;
        }

        .payment-panel {
            padding: 22px;
            height: 100%;
        }

        .payment-panel-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #163552;
            margin-bottom: 4px;
        }

        .payment-panel-subtitle {
            color: #68839f;
            font-size: 0.88rem;
            margin-bottom: 18px;
        }

        .deposit-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .deposit-card,
        .booking-card {
            padding: 18px;
            background: #fbfdff;
        }

        .deposit-meta,
        .booking-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: #68839f;
            font-size: 0.84rem;
            margin: 10px 0 0;
        }

        .deposit-amount {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f766e;
            margin-top: 8px;
        }

        .payment-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .payment-chip--success {
            background: #e8f8ef;
            color: #166534;
        }

        .payment-chip--warning {
            background: #fff4e8;
            color: #b45309;
        }

        .payment-chip--info {
            background: #eaf4ff;
            color: #0f5f92;
        }

        .empty-state {
            border: 1px dashed #cddbeb;
            border-radius: 16px;
            padding: 18px;
            background: #f9fbfe;
            color: #68839f;
        }
    </style>
@endpush

@section('content')
    @php
        $tongTienConLai = (float) $thongKe['tong_con_lai'];
        $tongTienChoXuLy = (float) $thongKe['tong_cho_xu_ly'];
        $danhSachCoTheDatCoc = $danhSachHoaDon->filter(fn ($hoaDon) => (bool) $hoaDon->co_the_dat_coc);
    @endphp

    <section class="payment-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h1 class="h3 mb-2">Thanh toán của tôi</h1>
                <p class="text-muted mb-0">
                    Hóa đơn, giao dịch và cọc phòng.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('booking.account') }}" class="btn btn-outline-secondary rounded-3">Hồ sơ</a>
                <a href="{{ route('booking.index') }}" class="btn btn-brand rounded-3">Đặt thêm phòng</a>
            </div>
        </div>

        <div class="payment-stat-grid">
            <div class="payment-stat-card">
                <div class="payment-stat-label">Tổng hóa đơn</div>
                <div class="payment-stat-value">{{ $thongKe['tong_hoa_don'] }}</div>
                <div class="small text-muted mt-2">Số hóa đơn hiện có</div>
            </div>
            <div class="payment-stat-card">
                <div class="payment-stat-label">Đã thanh toán</div>
                <div class="payment-stat-value text-success">{{ number_format((float) $thongKe['tong_da_thanh_toan'], 0, ',', '.') }} VNĐ</div>
                <div class="small text-muted mt-2">Đã xác nhận</div>
            </div>
            <div class="payment-stat-card">
                <div class="payment-stat-label">Chờ đối soát</div>
                <div class="payment-stat-value text-warning">{{ number_format($tongTienChoXuLy, 0, ',', '.') }} VNĐ</div>
                <div class="small text-muted mt-2">Đang chờ xác nhận</div>
            </div>
            <div class="payment-stat-card">
                <div class="payment-stat-label">Công nợ còn lại</div>
                <div class="payment-stat-value {{ $tongTienConLai > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($tongTienConLai, 0, ',', '.') }} VNĐ
                </div>
                <div class="small text-muted mt-2">Còn phải thanh toán</div>
            </div>
        </div>
    </section>

    @if($danhSachCoTheDatCoc->isNotEmpty())
        <section class="payment-panel mb-4">
            <div class="payment-panel-title">Cọc phòng</div>
            <div class="payment-panel-subtitle">Các đơn còn thiếu cọc.</div>

            <div class="deposit-grid">
                @foreach($danhSachCoTheDatCoc->take(3) as $hoaDon)
                    <article class="deposit-card">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold">{{ $hoaDon->ma_hoa_don }}</div>
                                <div class="small text-muted">{{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                            </div>
                            <span class="payment-chip payment-chip--info">Cọc</span>
                        </div>

                        <div class="deposit-amount">{{ number_format((float) $hoaDon->so_tien_con_thieu_coc, 0, ',', '.') }} VNĐ</div>
                        <div class="small text-muted mt-1">
                            Mức cọc gợi ý: {{ number_format((float) $hoaDon->goi_y_tien_coc, 0, ',', '.') }} VNĐ.
                        </div>

                        <div class="deposit-meta">
                            <span>Còn lại {{ number_format((float) $hoaDon->so_tien_con_lai, 0, ',', '.') }} VNĐ</span>
                            <span>Chờ đối soát {{ number_format((float) $hoaDon->so_tien_cho_xu_ly, 0, ',', '.') }} VNĐ</span>
                        </div>

                        <a href="{{ route('booking.hoa-don.show', ['hoaDon' => $hoaDon, 'che_do' => 'coc']) }}" class="btn btn-brand mt-3">Cọc ngay</a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <section class="payment-panel">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                    <div>
                        <div class="payment-panel-title">Thanh toán và hóa đơn của tôi</div>
                        <div class="payment-panel-subtitle">Danh sách hóa đơn hiện có.</div>
                    </div>
                    <a href="{{ route('booking.account') }}" class="btn btn-outline-secondary rounded-3">Sửa thông tin</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Hóa đơn</th>
                                <th>Đơn đặt phòng</th>
                                <th>Tổng tiền</th>
                                <th>Cọc gợi ý</th>
                                <th>Đã thu</th>
                                <th>Chờ đối soát</th>
                                <th>Còn lại</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachHoaDon as $hoaDon)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $hoaDon->ma_hoa_don }}</div>
                                        <div class="small text-muted">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                                        <div class="small text-muted">
                                            @php $phong = $hoaDon->datPhong?->chiTietDatPhong?->first()?->phong; @endphp
                                            {{ $phong ? 'Phòng ' . $phong->so_phong : 'Chưa gán phòng' }}
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        @if($hoaDon->goi_y_tien_coc > 0)
                                            <div class="fw-semibold text-info">{{ number_format((float) $hoaDon->goi_y_tien_coc, 0, ',', '.') }} VNĐ</div>
                                            @if($hoaDon->co_the_dat_coc)
                                                <div class="small text-muted">Còn thiếu {{ number_format((float) $hoaDon->so_tien_con_thieu_coc, 0, ',', '.') }} VNĐ</div>
                                            @else
                                                <div class="small text-muted">Đã đủ mức cọc gợi ý</div>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-success">{{ number_format((float) $hoaDon->so_tien_da_thanh_toan, 0, ',', '.') }} VNĐ</td>
                                    <td class="fw-semibold text-warning">{{ number_format((float) $hoaDon->so_tien_cho_xu_ly, 0, ',', '.') }} VNĐ</td>
                                    <td class="fw-semibold {{ (float) $hoaDon->so_tien_con_lai > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format((float) $hoaDon->so_tien_con_lai, 0, ',', '.') }} VNĐ
                                    </td>
                                    <td>
                                        <a href="{{ route('booking.hoa-don.show', ['hoaDon' => $hoaDon, 'che_do' => $hoaDon->co_the_dat_coc ? 'coc' : 'thanh_toan']) }}" class="btn btn-sm {{ $hoaDon->co_the_dat_coc ? 'btn-outline-primary' : 'btn-outline-secondary' }} rounded-3">
                                            {{ $hoaDon->co_the_dat_coc ? 'Cọc ngay' : ((float) $hoaDon->so_tien_con_lai > 0 || (float) $hoaDon->so_tien_cho_xu_ly > 0 ? 'Xem thanh toán' : 'Xem chi tiết') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Bạn chưa có hóa đơn nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="payment-panel mb-4">
                <div class="payment-panel-title">Lịch sử giao dịch gần đây</div>
                <div class="payment-panel-subtitle">Các giao dịch gần nhất.</div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã TT</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachThanhToan as $thanhToan)
                                @php
                                    $mauTrangThai = match ($thanhToan->trang_thai) {
                                        'thanh_cong' => 'text-success',
                                        'cho_xu_ly' => 'text-warning',
                                        'that_bai' => 'text-danger',
                                        default => 'text-muted',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</div>
                                        <div class="small text-muted">{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }}</div>
                                    </td>
                                    <td class="fw-semibold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        <div class="{{ $mauTrangThai }} fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</div>
                                        <div class="small text-muted">{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa có giao dịch thanh toán.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="payment-panel">
                <div class="payment-panel-title">Đơn đặt phòng gần đây</div>
                <div class="payment-panel-subtitle">Lịch ở gần đây.</div>

                @if($danhSachDatPhong->isEmpty())
                    <div class="empty-state">Bạn chưa có đơn đặt phòng nào.</div>
                @else
                    <div class="booking-grid">
                        @foreach($danhSachDatPhong as $datPhong)
                            @php
                                $phong = $datPhong->chiTietDatPhong->first()?->phong;
                                $hoaDon = $datPhong->hoaDon->where('trang_thai', '!=', 'da_huy')->first();
                            @endphp
                            <article class="booking-card">
                                <div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $datPhong->ma_dat_phong }}</div>
                                        <div class="small text-muted">{{ $phong ? 'Phòng ' . $phong->so_phong : 'Chưa gán phòng' }}</div>
                                    </div>
                                    @if($hoaDon)
                                        <a href="{{ route('booking.hoa-don.show', $hoaDon) }}" class="btn btn-sm btn-outline-secondary rounded-3">Mở hóa đơn</a>
                                    @endif
                                </div>

                                <div class="booking-meta">
                                    <span>{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }} - {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</span>
                                    <span>{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
