@extends('layouts.admin')

@section('title', 'Chi tiết hóa đơn')

@section('content')
    @php
        $mapTrangThai = [
            'chua_thanh_toan' => 'chip chip-danger',
            'thanh_toan_mot_phan' => 'chip chip-warning',
            'da_thanh_toan' => 'chip chip-success',
            'da_huy' => 'chip chip-neutral',
        ];
        $chipTrangThai = $mapTrangThai[$hoaDon->trang_thai] ?? 'chip chip-neutral';
        $danhSachDichVuSuDung = $hoaDon->datPhong?->suDungDichVu ?? collect();
        $coTheThemThanhToan = $hoaDon->trang_thai !== 'da_huy' && $soTienConLai > 0;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="section-title">Chi tiết hóa đơn {{ $hoaDon->ma_hoa_don }}</h2>
            <p class="section-subtitle">
                Xuất lúc {{ optional($hoaDon->thoi_diem_xuat)->format('d/m/Y H:i') ?? '-' }}
                @if($hoaDon->nguoiTao)
                    | Người lập: {{ $hoaDon->nguoiTao->ho_ten }}
                @endif
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('hoa-don.create') }}" class="btn btn-soft">Tạo hóa đơn mới</a>
            <a href="{{ route('hoa-don.index') }}" class="btn btn-gradient">Quay lại danh sách</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Thông tin đặt phòng</h5>
                            <p class="text-muted small mb-0">Dữ liệu đặt phòng gốc để đối soát với phòng, dịch vụ và công nợ.</p>
                        </div>
                        @if($hoaDon->datPhong)
                            <a href="{{ route('dat-phong.show', $hoaDon->datPhong) }}" class="btn btn-sm btn-outline-primary">Mở chi tiết đơn</a>
                        @endif
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="text-muted small">Mã đặt phòng</div>
                            <div class="fw-semibold">{{ $hoaDon->datPhong?->ma_dat_phong ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Khách hàng</div>
                            <div class="fw-semibold">{{ $hoaDon->datPhong?->khachHang?->ho_ten ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Số điện thoại</div>
                            <div class="fw-semibold">{{ $hoaDon->datPhong?->khachHang?->so_dien_thoai ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Giá / đêm</th>
                                    <th>Số đêm</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoaDon->datPhong?->chiTietDatPhong ?? [] as $chiTiet)
                                    <tr>
                                        <td>{{ $chiTiet->phong?->so_phong ? 'Phòng ' . $chiTiet->phong->so_phong : '-' }}</td>
                                        <td>{{ number_format((float) $chiTiet->gia_phong, 0, ',', '.') }} VNĐ</td>
                                        <td>{{ (int) $chiTiet->so_dem }}</td>
                                        <td class="fw-semibold">{{ number_format((float) $chiTiet->gia_phong * (int) $chiTiet->so_dem, 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Không có chi tiết phòng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Chi tiết dịch vụ</h5>
                            <p class="text-muted small mb-0">Tổng tiền dịch vụ được đồng bộ từ dữ liệu sử dụng thực tế của đơn đặt phòng.</p>
                        </div>
                        @if($hoaDon->datPhong)
                            <a href="{{ route('dat-phong.show', $hoaDon->datPhong) }}" class="btn btn-sm btn-outline-success">Ghi nhận dịch vụ</a>
                        @endif
                    </div>

                    @if($danhSachDichVuSuDung->isEmpty())
                        <div class="alert alert-light border mb-0">Hóa đơn này chưa có dịch vụ phát sinh.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Dịch vụ</th>
                                        <th>Thời điểm</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Thành tiền</th>
                                        <th>Người ghi nhận</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($danhSachDichVuSuDung as $suDungDichVu)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $suDungDichVu->dichVu?->ten_dich_vu ?? 'Dịch vụ đã xóa' }}</div>
                                                <div class="small text-muted">{{ $suDungDichVu->dichVu?->loai_dich_vu ?? $suDungDichVu->dichVu?->don_vi_tinh ?? '-' }}</div>
                                            </td>
                                            <td>{{ optional($suDungDichVu->thoi_diem_su_dung)->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td>{{ (int) $suDungDichVu->so_luong }} {{ $suDungDichVu->dichVu?->don_vi_tinh ?? '' }}</td>
                                            <td>{{ number_format((float) $suDungDichVu->don_gia, 0, ',', '.') }} VNĐ</td>
                                            <td class="fw-semibold">{{ number_format((float) $suDungDichVu->thanh_tien, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ $suDungDichVu->nguoiTao?->ho_ten ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Lịch sử thanh toán</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Mã TT</th>
                                    <th>Thời điểm</th>
                                    <th>Phương thức</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Người thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hoaDon->thanhToan as $thanhToan)
                                    @php
                                        $chip = match ($thanhToan->trang_thai) {
                                            'thanh_cong' => 'chip chip-success',
                                            'cho_xu_ly' => 'chip chip-warning',
                                            'that_bai' => 'chip chip-danger',
                                            default => 'chip chip-neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</td>
                                        <td>{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</td>
                                        <td>{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->phuong_thuc_thanh_toan) }}</td>
                                        <td class="fw-semibold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                        <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</span></td>
                                        <td>{{ $thanhToan->nguoiTao?->ho_ten ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Chưa có giao dịch thanh toán.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="premium-card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Tổng hợp hóa đơn</h5>

                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Tiền phòng</span>
                        <span class="fw-semibold">{{ number_format((float) $hoaDon->tong_tien_phong, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Tiền dịch vụ</span>
                        <span class="fw-semibold">{{ number_format((float) $hoaDon->tong_tien_dich_vu, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Giảm giá</span>
                        <span class="fw-semibold text-danger">-{{ number_format((float) $hoaDon->giam_gia, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Thuế</span>
                        <span class="fw-semibold">{{ number_format((float) $hoaDon->thue, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <hr>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="fw-bold">Tổng tiền</span>
                        <span class="fw-bold">{{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Đã thanh toán</span>
                        <span class="fw-semibold text-success">{{ number_format((float) $soTienDaThanhToan, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Còn lại</span>
                        <span class="fw-semibold {{ $soTienConLai > 0 ? 'text-danger' : 'text-success' }}">{{ number_format((float) $soTienConLai, 0, ',', '.') }} VNĐ</span>
                    </div>

                    <div class="mt-3"><span class="{{ $chipTrangThai }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</span></div>

                    <div class="alert alert-light border mt-3 mb-0 small">
                        Tiền dịch vụ sẽ tự động cập nhật nếu đơn đặt phòng phát sinh thêm, sửa hoặc xóa dịch vụ trước khi hóa đơn được thanh toán đầy đủ.
                    </div>

                    @if($soTienDaThanhToan > 0)
                        <div class="alert alert-warning mt-3 mb-0 small">
                            Hóa đơn đã có giao dịch thành công, vì vậy không thể chuyển sang trạng thái đã hủy.
                        </div>
                    @endif

                    <div class="mt-4">
                        <form method="POST" action="{{ route('hoa-don.cap-nhat-trang-thai', $hoaDon) }}">
                            @csrf
                            @method('PATCH')
                            <label class="form-label">Cập nhật trạng thái hóa đơn</label>
                            <div class="d-flex gap-2">
                                <select name="trang_thai" class="form-select">
                                    <option value="chua_thanh_toan" @selected($hoaDon->trang_thai === 'chua_thanh_toan')>Chưa thanh toán</option>
                                    <option value="thanh_toan_mot_phan" @selected($hoaDon->trang_thai === 'thanh_toan_mot_phan')>Thanh toán một phần</option>
                                    <option value="da_thanh_toan" @selected($hoaDon->trang_thai === 'da_thanh_toan')>Đã thanh toán</option>
                                    <option value="da_huy" @selected($hoaDon->trang_thai === 'da_huy') @disabled($soTienDaThanhToan > 0)>Đã hủy</option>
                                </select>
                                <button class="btn btn-outline-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="premium-card">
                <div class="card-body p-4">
                    @if($hoaDon->trang_thai === 'da_huy')
                        <h5 class="fw-bold mb-3">Thanh toán đã khóa</h5>
                        <div class="alert alert-secondary mb-0">Hóa đơn đã hủy nên không thể ghi nhận thêm thanh toán.</div>
                    @elseif(!$coTheThemThanhToan)
                        <h5 class="fw-bold mb-3">Thanh toán đã hoàn tất</h5>
                        <div class="alert alert-success mb-0">Hóa đơn này đã đủ tiền, không còn hiển thị biểu mẫu ghi nhận thanh toán nữa.</div>
                    @else
                        <h5 class="fw-bold mb-3">Thêm thanh toán</h5>

                        <form action="{{ route('thanh-toan.store') }}" method="POST" class="row g-3">
                            @csrf
                            <input type="hidden" name="hoa_don_id" value="{{ $hoaDon->id }}">
                            <input type="hidden" name="redirect_to" value="hoa_don_show">

                            <div class="col-12">
                                <label class="form-label">Số tiền thu</label>
                                <input type="number" min="1000" step="1000" name="so_tien" class="form-control @error('so_tien') is-invalid @enderror" value="{{ old('so_tien', $soTienConLai > 0 ? (int) $soTienConLai : '') }}" required>
                                @error('so_tien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Phương thức</label>
                                <select name="phuong_thuc_thanh_toan" class="form-select @error('phuong_thuc_thanh_toan') is-invalid @enderror" required>
                                    <option value="tien_mat" @selected(old('phuong_thuc_thanh_toan', 'tien_mat') === 'tien_mat')>Tiền mặt</option>
                                    <option value="chuyen_khoan" @selected(old('phuong_thuc_thanh_toan') === 'chuyen_khoan')>Chuyển khoản</option>
                                    <option value="the" @selected(old('phuong_thuc_thanh_toan') === 'the')>Thẻ</option>
                                    <option value="vi_dien_tu" @selected(old('phuong_thuc_thanh_toan') === 'vi_dien_tu')>Ví điện tử</option>
                                    <option value="khac" @selected(old('phuong_thuc_thanh_toan') === 'khac')>Khác</option>
                                </select>
                                @error('phuong_thuc_thanh_toan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Trạng thái giao dịch</label>
                                <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                                    <option value="thanh_cong" @selected(old('trang_thai', 'thanh_cong') === 'thanh_cong')>Thành công</option>
                                    <option value="cho_xu_ly" @selected(old('trang_thai') === 'cho_xu_ly')>Chờ xử lý</option>
                                    <option value="that_bai" @selected(old('trang_thai') === 'that_bai')>Thất bại</option>
                                </select>
                                @error('trang_thai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Thời điểm thanh toán</label>
                                <input type="datetime-local" name="thoi_diem_thanh_toan" class="form-control @error('thoi_diem_thanh_toan') is-invalid @enderror" value="{{ old('thoi_diem_thanh_toan', now()->format('Y-m-d\TH:i')) }}">
                                @error('thoi_diem_thanh_toan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" rows="2" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                                @error('ghi_chu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-gradient w-100">Ghi nhận thanh toán</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
