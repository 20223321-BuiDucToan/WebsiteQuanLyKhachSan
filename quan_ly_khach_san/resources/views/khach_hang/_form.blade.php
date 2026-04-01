<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">Họ tên</label>
        <input type="text" name="ho_ten" class="form-control @error('ho_ten') is-invalid @enderror" value="{{ old('ho_ten', $khachHang->ho_ten ?? '') }}" required>
        @error('ho_ten')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Giới tính</label>
        <select name="gioi_tinh" class="form-select @error('gioi_tinh') is-invalid @enderror">
            <option value="">-- Chọn --</option>
            <option value="nam" @selected(old('gioi_tinh', $khachHang->gioi_tinh ?? '') === 'nam')>Nam</option>
            <option value="nu" @selected(old('gioi_tinh', $khachHang->gioi_tinh ?? '') === 'nu')>Nữ</option>
            <option value="khac" @selected(old('gioi_tinh', $khachHang->gioi_tinh ?? '') === 'khac')>Khác</option>
        </select>
        @error('gioi_tinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Ngày sinh</label>
        <input type="date" name="ngay_sinh" class="form-control @error('ngay_sinh') is-invalid @enderror" value="{{ old('ngay_sinh', optional($khachHang->ngay_sinh ?? null)->toDateString()) }}">
        @error('ngay_sinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control @error('so_dien_thoai') is-invalid @enderror" value="{{ old('so_dien_thoai', $khachHang->so_dien_thoai ?? '') }}">
        @error('so_dien_thoai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $khachHang->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Quốc tịch</label>
        <input type="text" name="quoc_tich" class="form-control @error('quoc_tich') is-invalid @enderror" value="{{ old('quoc_tich', $khachHang->quoc_tich ?? '') }}">
        @error('quoc_tich')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Loại giấy tờ</label>
        <select name="loai_giay_to" class="form-select @error('loai_giay_to') is-invalid @enderror">
            <option value="">-- Chọn --</option>
            <option value="cccd" @selected(old('loai_giay_to', $khachHang->loai_giay_to ?? '') === 'cccd')>CCCD</option>
            <option value="cmnd" @selected(old('loai_giay_to', $khachHang->loai_giay_to ?? '') === 'cmnd')>CMND</option>
            <option value="passport" @selected(old('loai_giay_to', $khachHang->loai_giay_to ?? '') === 'passport')>Passport</option>
            <option value="khac" @selected(old('loai_giay_to', $khachHang->loai_giay_to ?? '') === 'khac')>Khác</option>
        </select>
        @error('loai_giay_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Số giấy tờ</label>
        <input type="text" name="so_giay_to" class="form-control @error('so_giay_to') is-invalid @enderror" value="{{ old('so_giay_to', $khachHang->so_giay_to ?? '') }}">
        @error('so_giay_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Hạng khách hàng</label>
        <select name="hang_khach_hang" class="form-select @error('hang_khach_hang') is-invalid @enderror" required>
            <option value="thuong" @selected(old('hang_khach_hang', $khachHang->hang_khach_hang ?? 'thuong') === 'thuong')>Thường</option>
            <option value="bac" @selected(old('hang_khach_hang', $khachHang->hang_khach_hang ?? '') === 'bac')>Bạc</option>
            <option value="vang" @selected(old('hang_khach_hang', $khachHang->hang_khach_hang ?? '') === 'vang')>Vàng</option>
            <option value="kim_cuong" @selected(old('hang_khach_hang', $khachHang->hang_khach_hang ?? '') === 'kim_cuong')>Kim cương</option>
        </select>
        @error('hang_khach_hang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Trạng thái</label>
        <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
            <option value="hoat_dong" @selected(old('trang_thai', $khachHang->trang_thai ?? 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
            <option value="tam_khoa" @selected(old('trang_thai', $khachHang->trang_thai ?? '') === 'tam_khoa')>Tạm khóa</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Địa chỉ</label>
        <input type="text" name="dia_chi" class="form-control @error('dia_chi') is-invalid @enderror" value="{{ old('dia_chi', $khachHang->dia_chi ?? '') }}">
        @error('dia_chi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu', $khachHang->ghi_chu ?? '') }}</textarea>
        @error('ghi_chu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
