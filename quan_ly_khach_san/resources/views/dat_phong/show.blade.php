@extends('layouts.admin')

@section('title', 'Chi tiết đặt phòng')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="section-title">Chi tiết đơn {{ $datPhong->ma_dat_phong }}</h2>
            <p class="section-subtitle">
                Tạo lúc {{ optional($datPhong->ngay_dat)->format('d/m/Y H:i') ?? '-' }}
                @if($datPhong->nguoiTao)
                    | Người tạo: {{ $datPhong->nguoiTao->ho_ten }}
                @endif
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('dat-phong.create') }}" class="btn btn-soft">Tạo đơn mới</a>
            <a href="{{ route('dat-phong.index') }}" class="btn btn-gradient">Quay lại danh sách</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Họ tên</div>
                            <div class="fw-semibold">{{ $datPhong->khachHang?->ho_ten ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Số điện thoại</div>
                            <div class="fw-semibold">{{ $datPhong->khachHang?->so_dien_thoai ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold">{{ $datPhong->khachHang?->email ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Danh sách phòng trong đơn</h5>

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
                                        <td class="fw-semibold">{{ $chiTiet->phong?->so_phong ? 'Phòng ' . $chiTiet->phong->so_phong : '-' }}</td>
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

                    <div class="text-end fw-bold fs-5 mt-2">Tổng tiền phòng tạm tính: {{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ</div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Yêu cầu và ghi chú</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Yêu cầu đặc biệt</div>
                        <div class="fw-semibold">{{ $datPhong->yeu_cau_dac_biet ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-muted small">Ghi chú nội bộ</div>
                        <div class="fw-semibold">{{ $datPhong->ghi_chu ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Thông tin lưu trú</h5>

                    <div class="mb-3">
                        <div class="text-muted small">Ngày nhận dự kiến</div>
                        <div class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_du_kien)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Ngày trả dự kiến</div>
                        <div class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_du_kien)->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Ngày nhận thực tế</div>
                        <div class="fw-semibold">{{ optional($datPhong->ngay_nhan_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Ngày trả thực tế</div>
                        <div class="fw-semibold">{{ optional($datPhong->ngay_tra_phong_thuc_te)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Số người ở</div>
                        <div class="fw-semibold">{{ (int) $datPhong->so_nguoi_lon }} người lớn, {{ (int) $datPhong->so_tre_em }} trẻ em</div>
                    </div>
                    <div>
                        <div class="text-muted small">Nguồn đặt</div>
                        <div class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($datPhong->nguon_dat ?? '-') }}</div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('hoa-don.create', ['dat_phong_id' => $datPhong->id]) }}" class="btn btn-outline-success w-100">
                            <i class="fa-solid fa-file-invoice-dollar me-2"></i>Tạo hóa đơn cho đơn này
                        </a>
                    </div>
                </div>
            </div>

            <div class="premium-card">
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

                        <button type="submit" class="btn btn-gradient w-100">Lưu trạng thái</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
