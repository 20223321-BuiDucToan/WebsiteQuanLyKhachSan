@extends('layouts.admin')

@section('title', 'Tạo đơn đặt phòng')

@section('content')
    <div class="mb-4">
        <h2 class="section-title">Tạo đơn đặt phòng</h2>
        <p class="section-subtitle">Nhập thông tin khách, chọn phòng và tạo đơn nội bộ.</p>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <form action="{{ route('dat-phong.store') }}" method="POST" class="row g-4">
                @csrf

                <div class="col-12">
                    <h5 class="fw-bold mb-0">Thông tin khách hàng</h5>
                    <small class="text-muted">Bắt buộc có ít nhất số điện thoại hoặc email.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" value="{{ old('ho_ten') }}" required>
                    @error('ho_ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai') }}">
                    @error('so_dien_thoai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 pt-2">
                    <h5 class="fw-bold mb-0">Thông tin đặt phòng</h5>
                    <small class="text-muted">Chỉ hiển thị các phòng còn sẵn sàng hoạt động.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Phòng</label>
                    <select name="phong_id" class="form-select @error('phong_id') is-invalid @enderror" required>
                        <option value="">-- Chọn phòng --</option>
                        @foreach($danhSachPhong as $phong)
                            @php
                                $gia = $phong->gia_mac_dinh ?? $phong->loaiPhong?->gia_mot_dem;
                                $sucChua = $phong->loaiPhong?->so_nguoi_toi_da;
                            @endphp
                            <option value="{{ $phong->id }}" @selected((string) old('phong_id') === (string) $phong->id)>
                                Phòng {{ $phong->so_phong }} - {{ $phong->loaiPhong?->ten_loai_phong ?? 'Không rõ loại' }}
                                @if($gia)
                                    ({{ number_format((float) $gia, 0, ',', '.') }} VNĐ/đêm)
                                @endif
                                @if($sucChua)
                                    - tối đa {{ $sucChua }} khách
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('phong_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ngày nhận</label>
                    <input type="date" name="ngay_nhan" class="form-control @error('ngay_nhan') is-invalid @enderror" value="{{ old('ngay_nhan', now()->toDateString()) }}" required>
                    @error('ngay_nhan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Ngày trả</label>
                    <input type="date" name="ngay_tra" class="form-control @error('ngay_tra') is-invalid @enderror" value="{{ old('ngay_tra', now()->addDay()->toDateString()) }}" required>
                    @error('ngay_tra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Người lớn</label>
                    <input type="number" name="so_nguoi_lon" min="1" max="20" class="form-control @error('so_nguoi_lon') is-invalid @enderror" value="{{ old('so_nguoi_lon', 1) }}" required>
                    @error('so_nguoi_lon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Trẻ em</label>
                    <input type="number" name="so_tre_em" min="0" max="20" class="form-control @error('so_tre_em') is-invalid @enderror" value="{{ old('so_tre_em', 0) }}">
                    @error('so_tre_em')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nguồn đặt</label>
                    <select name="nguon_dat" class="form-select @error('nguon_dat') is-invalid @enderror" required>
                        <option value="truc_tiep" @selected(old('nguon_dat', 'truc_tiep') === 'truc_tiep')>Trực tiếp</option>
                        <option value="website" @selected(old('nguon_dat') === 'website')>Website</option>
                        <option value="dien_thoai" @selected(old('nguon_dat') === 'dien_thoai')>Điện thoại</option>
                        <option value="zalo" @selected(old('nguon_dat') === 'zalo')>Zalo</option>
                        <option value="khac" @selected(old('nguon_dat') === 'khac')>Khác</option>
                    </select>
                    @error('nguon_dat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Trạng thái ban đầu</label>
                    <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                        <option value="cho_xac_nhan" @selected(old('trang_thai', 'da_xac_nhan') === 'cho_xac_nhan')>Chờ xác nhận</option>
                        <option value="da_xac_nhan" @selected(old('trang_thai', 'da_xac_nhan') === 'da_xac_nhan')>Đã xác nhận</option>
                        <option value="da_nhan_phong" @selected(old('trang_thai') === 'da_nhan_phong')>Đã nhận phòng</option>
                    </select>
                    @error('trang_thai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Yêu cầu đặc biệt</label>
                    <input type="text" name="yeu_cau_dac_biet" class="form-control @error('yeu_cau_dac_biet') is-invalid @enderror" value="{{ old('yeu_cau_dac_biet') }}" placeholder="Ví dụ: phòng không hút thuốc, giường đôi...">
                    @error('yeu_cau_dac_biet')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Ghi chú nội bộ</label>
                    <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient"><i class="fa-solid fa-floppy-disk me-2"></i>Lưu đơn đặt phòng</button>
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-soft">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
