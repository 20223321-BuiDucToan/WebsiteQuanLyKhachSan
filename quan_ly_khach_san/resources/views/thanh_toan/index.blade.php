@extends('layouts.admin')

@section('title', 'Quản lý thanh toán')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Quản lý thanh toán</h2>
        <p class="section-subtitle">Ghi nhận giao dịch thu tiền và theo dõi trạng thái xử lý.</p>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Tạo giao dịch thanh toán</h5>

            <form action="{{ route('thanh-toan.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-lg-4">
                    <label class="form-label">Hóa đơn cần thu</label>
                    <select name="hoa_don_id" class="form-select @error('hoa_don_id') is-invalid @enderror" required>
                        <option value="">-- Chọn hóa đơn --</option>
                        @foreach($danhSachHoaDon as $hoaDon)
                            <option value="{{ $hoaDon->id }}" @selected((string) old('hoa_don_id') === (string) $hoaDon->id)>
                                {{ $hoaDon->ma_hoa_don }} - {{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ
                            </option>
                        @endforeach
                    </select>
                    @error('hoa_don_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Số tiền</label>
                    <input type="number" min="1000" step="1000" name="so_tien" class="form-control @error('so_tien') is-invalid @enderror" value="{{ old('so_tien') }}" required>
                    @error('so_tien')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
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

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                        <option value="thanh_cong" @selected(old('trang_thai', 'thanh_cong') === 'thanh_cong')>Thành công</option>
                        <option value="cho_xu_ly" @selected(old('trang_thai') === 'cho_xu_ly')>Chờ xử lý</option>
                        <option value="that_bai" @selected(old('trang_thai') === 'that_bai')>Thất bại</option>
                    </select>
                    @error('trang_thai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Thời điểm</label>
                    <input type="datetime-local" name="thoi_diem_thanh_toan" class="form-control @error('thoi_diem_thanh_toan') is-invalid @enderror" value="{{ old('thoi_diem_thanh_toan', now()->format('Y-m-d\TH:i')) }}">
                    @error('thoi_diem_thanh_toan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="ghi_chu" class="form-control @error('ghi_chu') is-invalid @enderror" value="{{ old('ghi_chu') }}" placeholder="Nội dung bổ sung (nếu có)">
                    @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-gradient" type="submit"><i class="fa-solid fa-money-check-dollar me-2"></i>Ghi nhận thanh toán</button>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã giao dịch, mã hóa đơn, tên khách">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="thanh_cong" @selected($trangThai === 'thanh_cong')>Thành công</option>
                        <option value="cho_xu_ly" @selected($trangThai === 'cho_xu_ly')>Chờ xử lý</option>
                        <option value="that_bai" @selected($trangThai === 'that_bai')>Thất bại</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Phương thức</label>
                    <select name="phuong_thuc" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="tien_mat" @selected($phuongThuc === 'tien_mat')>Tiền mặt</option>
                        <option value="chuyen_khoan" @selected($phuongThuc === 'chuyen_khoan')>Chuyển khoản</option>
                        <option value="the" @selected($phuongThuc === 'the')>Thẻ</option>
                        <option value="vi_dien_tu" @selected($phuongThuc === 'vi_dien_tu')>Ví điện tử</option>
                        <option value="khac" @selected($phuongThuc === 'khac')>Khác</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}">
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-lg-1 d-flex align-items-end">
                    <a href="{{ route('thanh-toan.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mã thanh toán</th>
                            <th>Hóa đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Phương thức</th>
                            <th>Thời điểm</th>
                            <th>Trạng thái</th>
                            <th>Người thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachThanhToan as $thanhToan)
                            @php
                                $mapTrangThai = [
                                    'thanh_cong' => 'chip chip-success',
                                    'cho_xu_ly' => 'chip chip-warning',
                                    'that_bai' => 'chip chip-danger',
                                ];
                                $chip = $mapTrangThai[$thanhToan->trang_thai] ?? 'chip chip-neutral';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $thanhToan->ma_thanh_toan }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }}</div>
                                    <div class="small text-muted">{{ $thanhToan->hoaDon?->datPhong?->ma_dat_phong ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->ho_ten ?? '-' }}</div>
                                    <div class="small text-muted">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->so_dien_thoai ?? '-' }}</div>
                                </td>
                                <td class="fw-bold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                <td>{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->phuong_thuc_thanh_toan) }}</td>
                                <td>{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</span></td>
                                <td>{{ $thanhToan->nguoiTao?->ho_ten ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Chưa có giao dịch nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachThanhToan->links() }}</div>
        </div>
    </div>
@endsection
