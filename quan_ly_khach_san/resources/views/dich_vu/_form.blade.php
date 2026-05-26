@php
    $dichVuHienTai = $dichVu ?? new \App\Models\DichVu();
@endphp

<div class="col-12 d-grid gap-3">
    <section class="form-section">
        <h3 class="form-section__title">Thông tin dịch vụ</h3>
        <p class="form-section__description">Nhập tên, loại và đơn vị tính để nhân viên ghi nhận dịch vụ nhanh trên từng đơn đặt phòng.</p>

        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Mã dịch vụ</label>
                <div class="field-static">{{ $dichVuHienTai->ma_dich_vu ?: 'Tự động tạo khi lưu' }}</div>
            </div>

            <div class="col-lg-4">
                <label class="form-label">Tên dịch vụ</label>
                <input
                    type="text"
                    name="ten_dich_vu"
                    class="form-control @error('ten_dich_vu') is-invalid @enderror"
                    value="{{ old('ten_dich_vu', $dichVuHienTai->ten_dich_vu) }}"
                    placeholder="Ví dụ: Buffet sáng"
                    required
                >
                @error('ten_dich_vu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-4">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                    <option value="hoat_dong" @selected(old('trang_thai', $dichVuHienTai->trang_thai ?: 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
                    <option value="tam_ngung" @selected(old('trang_thai', $dichVuHienTai->trang_thai) === 'tam_ngung')>Tạm ngưng</option>
                </select>
                @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-4">
                <label class="form-label">Loại dịch vụ</label>
                <input
                    type="text"
                    name="loai_dich_vu"
                    class="form-control @error('loai_dich_vu') is-invalid @enderror"
                    value="{{ old('loai_dich_vu', $dichVuHienTai->loai_dich_vu) }}"
                    placeholder="Ví dụ: Ẩm thực, vận chuyển"
                >
                @error('loai_dich_vu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-4">
                <label class="form-label">Đơn vị tính</label>
                <input
                    type="text"
                    name="don_vi_tinh"
                    class="form-control @error('don_vi_tinh') is-invalid @enderror"
                    value="{{ old('don_vi_tinh', $dichVuHienTai->don_vi_tinh ?: 'lần') }}"
                    placeholder="Ví dụ: lần, suất, chai"
                    required
                >
                @error('don_vi_tinh')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-lg-4">
                <label class="form-label">Đơn giá</label>
                <input
                    type="number"
                    min="0"
                    step="1000"
                    name="don_gia"
                    class="form-control @error('don_gia') is-invalid @enderror"
                    value="{{ old('don_gia', $dichVuHienTai->don_gia !== null ? (float) $dichVuHienTai->don_gia : '') }}"
                    required
                >
                @error('don_gia')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <h3 class="form-section__title">Mô tả và lưu ý</h3>
        <p class="form-section__description">Bạn có thể ghi ngắn gọn nội dung phục vụ hoặc các lưu ý đặc biệt để nhân viên thao tác thống nhất.</p>

        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Mô tả</label>
                <textarea
                    name="mo_ta"
                    rows="4"
                    class="form-control @error('mo_ta') is-invalid @enderror"
                    placeholder="Ví dụ: Phục vụ tại phòng từ 6:00 đến 10:00, áp dụng theo số khách thực tế..."
                >{{ old('mo_ta', $dichVuHienTai->mo_ta) }}</textarea>
                @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <ul class="form-hint-list">
                    <li>Nên đặt tên ngắn gọn để dễ tìm kiếm khi ghi nhận dịch vụ.</li>
                    <li>Đơn vị tính nên thống nhất với cách tính tiền thực tế của khách sạn.</li>
                    <li>Giữ trạng thái “Tạm ngưng” nếu muốn ẩn dịch vụ mà không mất dữ liệu cũ.</li>
                </ul>
            </div>
        </div>
    </section>
</div>
