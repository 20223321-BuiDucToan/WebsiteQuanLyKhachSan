@extends('layouts.admin')

@section('title', 'Tạo hóa đơn')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Tạo hóa đơn</h2>
        <p class="section-subtitle">Chọn đơn đặt phòng và lập hóa đơn thanh toán.</p>
    </div>

    <div class="premium-card mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-8">
                    <label class="form-label">Đơn đặt phòng</label>
                    <select name="dat_phong_id" class="form-select" required>
                        <option value="">-- Chọn đơn đặt phòng --</option>
                        @foreach($danhSachDatPhong as $datPhong)
                            @php
                                $chiTiet = $datPhong->chiTietDatPhong->first();
                            @endphp
                            <option value="{{ $datPhong->id }}" @selected((string) request('dat_phong_id') === (string) $datPhong->id)>
                                {{ $datPhong->ma_dat_phong }} - {{ $datPhong->khachHang?->ho_ten ?? 'Khách lẻ' }}
                                @if($chiTiet?->phong)
                                    - Phòng {{ $chiTiet->phong->so_phong }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <button type="submit" class="btn btn-soft w-100">Tải thông tin</button>
                </div>

                <div class="col-lg-2">
                    <a href="{{ route('hoa-don.index') }}" class="btn btn-outline-secondary w-100">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $tongTienDichVuOld = (float) old('tong_tien_dich_vu', 0);
        $giamGiaOld = (float) old('giam_gia', 0);
        $thueOld = (float) old('thue', 0);
        $tongTamTinh = max(0, (float) $tongTienPhong + $tongTienDichVuOld - $giamGiaOld + $thueOld);
    @endphp

    <div class="premium-card">
        <div class="card-body p-4">
            @if(!$datPhongDuocChon)
                <div class="alert alert-info mb-0">Vui lòng chọn đơn đặt phòng ở trên để tạo hóa đơn.</div>
            @else
                <form action="{{ route('hoa-don.store') }}" method="POST" class="row g-4">
                    @csrf
                    <input type="hidden" name="dat_phong_id" value="{{ $datPhongDuocChon->id }}">

                    <div class="col-12">
                        <h5 class="fw-bold mb-3">Thông tin đơn đặt phòng</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Mã đặt phòng</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->ma_dat_phong }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Khách hàng</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->khachHang?->ho_ten ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Số điện thoại</div>
                                <div class="fw-semibold">{{ $datPhongDuocChon->khachHang?->so_dien_thoai ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tổng tiền phòng</label>
                        <input type="text" class="form-control" value="{{ number_format((float) $tongTienPhong, 0, ',', '.') }} VNĐ" disabled>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tổng tiền dịch vụ</label>
                        <input type="number" min="0" step="1000" name="tong_tien_dich_vu" class="form-control @error('tong_tien_dich_vu') is-invalid @enderror" value="{{ old('tong_tien_dich_vu', 0) }}">
                        @error('tong_tien_dich_vu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Giảm giá</label>
                        <input type="number" min="0" step="1000" name="giam_gia" class="form-control @error('giam_gia') is-invalid @enderror" value="{{ old('giam_gia', 0) }}">
                        @error('giam_gia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Thuế</label>
                        <input type="number" min="0" step="1000" name="thue" class="form-control @error('thue') is-invalid @enderror" value="{{ old('thue', 0) }}">
                        @error('thue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                        @error('ghi_chu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            Tổng thanh toán tạm tính: <span class="fw-bold fs-5">{{ number_format((float) $tongTamTinh, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-gradient"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Tạo hóa đơn</button>
                        <a href="{{ route('hoa-don.index') }}" class="btn btn-soft">Hủy</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
