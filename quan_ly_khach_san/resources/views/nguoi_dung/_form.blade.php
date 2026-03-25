<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Họ tên</label>
        <input type="text" name="ho_ten" class="form-control rounded-4 @error('ho_ten') is-invalid @enderror"
            value="{{ old('ho_ten', $nguoiDung->ho_ten ?? '') }}">
        @error('ho_ten')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Tên đăng nhập</label>
        <input type="text" name="ten_dang_nhap" class="form-control rounded-4 @error('ten_dang_nhap') is-invalid @enderror"
            value="{{ old('ten_dang_nhap', $nguoiDung->ten_dang_nhap ?? '') }}">
        @error('ten_dang_nhap')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control rounded-4 @error('email') is-invalid @enderror"
            value="{{ old('email', $nguoiDung->email ?? '') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control rounded-4 @error('so_dien_thoai') is-invalid @enderror"
            value="{{ old('so_dien_thoai', $nguoiDung->so_dien_thoai ?? '') }}">
        @error('so_dien_thoai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Mật khẩu
            @isset($nguoiDung)
                <small class="text-muted">(để trống nếu không đổi)</small>
            @endisset
        </label>
        <input type="password" name="password" class="form-control rounded-4 @error('password') is-invalid @enderror">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
        <input type="password" name="password_confirmation" class="form-control rounded-4">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Vai trò</label>
        <select name="vai_tro" class="form-select rounded-4 @error('vai_tro') is-invalid @enderror">
            <option value="admin" {{ old('vai_tro', $nguoiDung->vai_tro ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="nhan_vien" {{ old('vai_tro', $nguoiDung->vai_tro ?? '') == 'nhan_vien' ? 'selected' : '' }}>Nhân viên</option>
        </select>
        @error('vai_tro')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Trạng thái</label>
        <select name="trang_thai" class="form-select rounded-4 @error('trang_thai') is-invalid @enderror">
            <option value="hoat_dong" {{ old('trang_thai', $nguoiDung->trang_thai ?? '') == 'hoat_dong' ? 'selected' : '' }}>Hoạt động</option>
            <option value="tam_khoa" {{ old('trang_thai', $nguoiDung->trang_thai ?? '') == 'tam_khoa' ? 'selected' : '' }}>Tạm khóa</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Địa chỉ</label>
        <textarea name="dia_chi" rows="3" class="form-control rounded-4 @error('dia_chi') is-invalid @enderror">{{ old('dia_chi', $nguoiDung->dia_chi ?? '') }}</textarea>
        @error('dia_chi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>