@php
    $dichVuHienTai = $dichVu ?? new \App\Models\DichVu();
@endphp

<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label">Ma dich vu</label>
        <input
            type="text"
            class="form-control"
            value="{{ $dichVuHienTai->ma_dich_vu ?: 'He thong tu dong sinh khi tao moi' }}"
            disabled
        >
    </div>

    <div class="col-lg-6">
        <label class="form-label">Trạng thái</label>
        <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
            <option value="hoat_dong" @selected(old('trang_thai', $dichVuHienTai->trang_thai ?: 'hoat_dong') === 'hoat_dong')>Hoat dong</option>
            <option value="tam_ngung" @selected(old('trang_thai', $dichVuHienTai->trang_thai) === 'tam_ngung')>Tam ngung</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-6">
        <label class="form-label">Ten dich vu</label>
        <input
            type="text"
            name="ten_dich_vu"
            class="form-control @error('ten_dich_vu') is-invalid @enderror"
            value="{{ old('ten_dich_vu', $dichVuHienTai->ten_dich_vu) }}"
            placeholder="Vi du: Minibar, giat ui, spa, dua don san bay"
            required
        >
        @error('ten_dich_vu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-3">
        <label class="form-label">Loai dich vu</label>
        <input
            type="text"
            name="loai_dich_vu"
            class="form-control @error('loai_dich_vu') is-invalid @enderror"
            value="{{ old('loai_dich_vu', $dichVuHienTai->loai_dich_vu) }}"
            placeholder="Minibar, giat ui..."
        >
        @error('loai_dich_vu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-3">
        <label class="form-label">Don vi tinh</label>
        <input
            type="text"
            name="don_vi_tinh"
            class="form-control @error('don_vi_tinh') is-invalid @enderror"
            value="{{ old('don_vi_tinh', $dichVuHienTai->don_vi_tinh ?: 'lan') }}"
            placeholder="lan, chai, suat, gio"
            required
        >
        @error('don_vi_tinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Don gia</label>
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

    <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea
            name="mo_ta"
            rows="4"
            class="form-control @error('mo_ta') is-invalid @enderror"
            placeholder="Mô tả ngắn gọn về nội dung dịch vụ, quy tắc áp dụng, lưu ý vận hành..."
        >{{ old('mo_ta', $dichVuHienTai->mo_ta) }}</textarea>
        @error('mo_ta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
