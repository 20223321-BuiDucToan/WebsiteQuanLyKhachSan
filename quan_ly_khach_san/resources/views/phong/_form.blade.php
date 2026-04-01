<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">Số phòng</label>
        <input
            type="text"
            name="so_phong"
            class="form-control @error('so_phong') is-invalid @enderror"
            value="{{ old('so_phong', $phong->so_phong ?? '') }}"
            required
        >
        @error('so_phong')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Loại phòng</label>
        <select name="loai_phong_id" class="form-select @error('loai_phong_id') is-invalid @enderror" required>
            <option value="">-- Chọn loại phòng --</option>
            @foreach($danhSachLoaiPhong as $loaiPhong)
                <option value="{{ $loaiPhong->id }}" @selected((string) old('loai_phong_id', $phong->loai_phong_id ?? '') === (string) $loaiPhong->id)>
                    {{ $loaiPhong->ten_loai_phong }} ({{ number_format((float) $loaiPhong->gia_mot_dem, 0, ',', '.') }} VNĐ/đêm)
                </option>
            @endforeach
        </select>
        @error('loai_phong_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tầng</label>
        <input
            type="number"
            min="0"
            max="100"
            name="tang"
            class="form-control @error('tang') is-invalid @enderror"
            value="{{ old('tang', $phong->tang ?? '') }}"
        >
        @error('tang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Trạng thái phòng</label>
        <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
            <option value="trong" @selected(old('trang_thai', $phong->trang_thai ?? 'trong') === 'trong')>Trống</option>
            <option value="da_dat" @selected(old('trang_thai', $phong->trang_thai ?? '') === 'da_dat')>Đã đặt</option>
            <option value="dang_su_dung" @selected(old('trang_thai', $phong->trang_thai ?? '') === 'dang_su_dung')>Đang sử dụng</option>
            <option value="don_dep" @selected(old('trang_thai', $phong->trang_thai ?? '') === 'don_dep')>Dọn dẹp</option>
            <option value="bao_tri" @selected(old('trang_thai', $phong->trang_thai ?? '') === 'bao_tri')>Bảo trì</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tình trạng vệ sinh</label>
        <select name="tinh_trang_ve_sinh" class="form-select @error('tinh_trang_ve_sinh') is-invalid @enderror" required>
            <option value="sach" @selected(old('tinh_trang_ve_sinh', $phong->tinh_trang_ve_sinh ?? 'sach') === 'sach')>Sạch</option>
            <option value="can_don" @selected(old('tinh_trang_ve_sinh', $phong->tinh_trang_ve_sinh ?? '') === 'can_don')>Cần dọn</option>
            <option value="dang_don" @selected(old('tinh_trang_ve_sinh', $phong->tinh_trang_ve_sinh ?? '') === 'dang_don')>Đang dọn</option>
            <option value="ban" @selected(old('tinh_trang_ve_sinh', $phong->tinh_trang_ve_sinh ?? '') === 'ban')>Bẩn</option>
        </select>
        @error('tinh_trang_ve_sinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tình trạng hoạt động</label>
        <select name="tinh_trang_hoat_dong" class="form-select @error('tinh_trang_hoat_dong') is-invalid @enderror" required>
            <option value="hoat_dong" @selected(old('tinh_trang_hoat_dong', $phong->tinh_trang_hoat_dong ?? 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
            <option value="tam_ngung" @selected(old('tinh_trang_hoat_dong', $phong->tinh_trang_hoat_dong ?? '') === 'tam_ngung')>Tạm ngưng</option>
        </select>
        @error('tinh_trang_hoat_dong')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Giá mặc định (VNĐ / đêm)</label>
        <input
            type="number"
            min="0"
            step="1000"
            name="gia_mac_dinh"
            class="form-control @error('gia_mac_dinh') is-invalid @enderror"
            value="{{ old('gia_mac_dinh', $phong->gia_mac_dinh ?? '') }}"
        >
        @error('gia_mac_dinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu', $phong->ghi_chu ?? '') }}</textarea>
        @error('ghi_chu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
