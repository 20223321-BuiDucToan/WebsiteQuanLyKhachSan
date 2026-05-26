@php
    $loaiPhongHienTai = $loaiPhong ?? new \App\Models\LoaiPhong();
@endphp

<div class="d-grid gap-3">
    <section class="form-section">
        <h3 class="form-section__title">Thông tin cơ bản</h3>
        <p class="form-section__description">Chỉ nhập những dữ liệu cần thiết để hệ thống tính giá, sức chứa và tạo phòng từ loại phòng này.</p>

        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Mã loại phòng</label>
                <div class="field-static">{{ $loaiPhongHienTai->ma_loai_phong ?: 'Tự động tạo khi lưu' }}</div>
                <div class="field-note">Bạn không cần nhập mã thủ công khi tạo mới.</div>
            </div>

            <div class="col-lg-4">
                <label class="form-label">Tên loại phòng</label>
                <input
                    type="text"
                    name="ten_loai_phong"
                    class="form-control @error('ten_loai_phong') is-invalid @enderror"
                    value="{{ old('ten_loai_phong', $loaiPhongHienTai->ten_loai_phong) }}"
                    placeholder="Ví dụ: Deluxe hướng biển"
                    required
                >
                @error('ten_loai_phong')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-4">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                    <option value="hoat_dong" @selected(old('trang_thai', $loaiPhongHienTai->trang_thai ?: 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
                    <option value="tam_ngung" @selected(old('trang_thai', $loaiPhongHienTai->trang_thai) === 'tam_ngung')>Tạm ngưng</option>
                </select>
                @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-3">
                <label class="form-label">Giá chuẩn mỗi đêm</label>
                <input
                    type="number"
                    min="0"
                    step="1000"
                    name="gia_mot_dem"
                    class="form-control @error('gia_mot_dem') is-invalid @enderror"
                    value="{{ old('gia_mot_dem', $loaiPhongHienTai->gia_mot_dem !== null ? (float) $loaiPhongHienTai->gia_mot_dem : '') }}"
                    required
                >
                @error('gia_mot_dem')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-3">
                <label class="form-label">Số người tối đa</label>
                <input
                    type="number"
                    min="1"
                    max="20"
                    name="so_nguoi_toi_da"
                    class="form-control @error('so_nguoi_toi_da') is-invalid @enderror"
                    value="{{ old('so_nguoi_toi_da', $loaiPhongHienTai->so_nguoi_toi_da ?: 2) }}"
                    required
                >
                @error('so_nguoi_toi_da')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-3">
                <label class="form-label">Diện tích (m²)</label>
                <input
                    type="number"
                    min="0"
                    step="0.1"
                    name="dien_tich"
                    class="form-control @error('dien_tich') is-invalid @enderror"
                    value="{{ old('dien_tich', $loaiPhongHienTai->dien_tich !== null ? (float) $loaiPhongHienTai->dien_tich : '') }}"
                    placeholder="Ví dụ: 32"
                >
                @error('dien_tich')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-3">
                <label class="form-label">Số giường</label>
                <input
                    type="number"
                    min="1"
                    max="20"
                    name="so_giuong"
                    class="form-control @error('so_giuong') is-invalid @enderror"
                    value="{{ old('so_giuong', $loaiPhongHienTai->so_giuong ?: 1) }}"
                    required
                >
                @error('so_giuong')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-6">
                <label class="form-label">Loại giường</label>
                <input
                    type="text"
                    name="loai_giuong"
                    class="form-control @error('loai_giuong') is-invalid @enderror"
                    value="{{ old('loai_giuong', $loaiPhongHienTai->loai_giuong) }}"
                    placeholder="Ví dụ: King, Queen, Twin"
                >
                @error('loai_giuong')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-6">
                <label class="form-label">Số phòng tắm</label>
                <input
                    type="number"
                    min="1"
                    max="20"
                    name="so_phong_tam"
                    class="form-control @error('so_phong_tam') is-invalid @enderror"
                    value="{{ old('so_phong_tam', $loaiPhongHienTai->so_phong_tam ?: 1) }}"
                    required
                >
                @error('so_phong_tam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <h3 class="form-section__title">Tiện ích và mô tả</h3>
        <p class="form-section__description">Chọn nhanh các tiện ích nổi bật để nhân viên dễ tư vấn và khách dễ phân biệt từng hạng phòng.</p>

        <div class="checkbox-card-grid">
            <label class="checkbox-card">
                <input type="checkbox" name="co_ban_cong" value="1" @checked(old('co_ban_cong', $loaiPhongHienTai->co_ban_cong))>
                <span>
                    <strong>Có ban công</strong>
                    <small>Phù hợp với phòng nghỉ dưỡng hoặc phòng có view đẹp.</small>
                </span>
            </label>

            <label class="checkbox-card">
                <input type="checkbox" name="co_bep_rieng" value="1" @checked(old('co_bep_rieng', $loaiPhongHienTai->co_bep_rieng))>
                <span>
                    <strong>Có bếp riêng</strong>
                    <small>Hữu ích cho khách ở dài ngày hoặc gia đình.</small>
                </span>
            </label>

            <label class="checkbox-card">
                <input type="checkbox" name="co_huong_bien" value="1" @checked(old('co_huong_bien', $loaiPhongHienTai->co_huong_bien))>
                <span>
                    <strong>Hướng biển</strong>
                    <small>Dễ phân loại phòng cao cấp và hỗ trợ bán giá tốt hơn.</small>
                </span>
            </label>
        </div>

        <div class="mt-3">
            <label class="form-label">Mô tả ngắn</label>
            <textarea
                name="mo_ta"
                rows="4"
                class="form-control @error('mo_ta') is-invalid @enderror"
                placeholder="Mô tả ngắn gọn về nội thất, điểm nổi bật và đối tượng khách phù hợp..."
            >{{ old('mo_ta', $loaiPhongHienTai->mo_ta) }}</textarea>
            @error('mo_ta')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </section>
</div>
