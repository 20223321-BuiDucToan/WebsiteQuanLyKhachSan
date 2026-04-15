@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="section-title">Chi tiết khách hàng {{ $khachHang->ma_khach_hang }}</h2>
            <p class="section-subtitle">Thông tin hồ sơ và lịch sử đặt phòng của khách hàng.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('khach-hang.edit', $khachHang) }}" class="btn btn-gradient"><i class="fa-solid fa-pen me-2"></i>Cập nhật thông tin</a>
            <a href="{{ route('khach-hang.index') }}" class="btn btn-soft">Quay lại danh sách</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin hồ sơ</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Họ tên</div>
                        <div class="fw-semibold">{{ $khachHang->ho_ten }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Giới tính</div>
                        <div class="fw-semibold">{{ $khachHang->gioi_tinh ?: '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Ngày sinh</div>
                        <div class="fw-semibold">{{ optional($khachHang->ngay_sinh)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Liên hệ</div>
                        <div class="fw-semibold">{{ $khachHang->so_dien_thoai ?: '-' }}</div>
                        <div class="fw-semibold">{{ $khachHang->email ?: '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Giấy tờ</div>
                        <div class="fw-semibold">
                            {{ strtoupper((string) $khachHang->loai_giay_to) ?: '-' }}
                            @if($khachHang->so_giay_to)
                                - {{ $khachHang->so_giay_to }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Quốc tịch</div>
                        <div class="fw-semibold">{{ $khachHang->quoc_tich ?: '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Hạng khách hàng</div>
                        <div class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($khachHang->hang_khach_hang) }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Trạng thái</div>
                        <div class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($khachHang->trang_thai) }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Ghi chú</div>
                        <div class="fw-semibold">{{ $khachHang->ghi_chu ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="premium-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Lịch sử đặt phòng</h5>
                        <div class="text-muted small">Tổng số đơn: {{ $khachHang->datPhong->count() }}</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Lịch lưu trú</th>
                                    <th>Phòng</th>
                                    <th>Nguồn đặt</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($khachHang->datPhong as $datPhong)
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
                                    <tr>
                                        <td class="fw-semibold"><a href="{{ route('dat-phong.show', $datPhong) }}" class="text-decoration-none">{{ $datPhong->ma_dat_phong }}</a></td>
                                        <td>{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }} - {{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</td>
                                        <td>{{ $phong?->so_phong ? 'Phòng ' . $phong->so_phong : '-' }}</td>
                                        <td>{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? '-') }}</td>
                                        <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->trang_thai) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Khách hàng chưa có lịch sử đặt phòng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
