@php
    $loaiPhongHienTai = $loaiPhong ?? new \App\Models\LoaiPhong();
@endphp

<div class="row g-4">
    <div class="col-lg-6">
        <label class="form-label">Mã loại phòng</label>
        <input
            type="text"
            class="form-control"
            value="{{ $loaiPhongHienTai->ma_loai_phong ?: 'Hệ thống tự động sinh khi tạo mới' }}"
            disabled
        >
    </div>

    <div class="col-lg-6">
        <label class="form-label">Trạng thái</label>
        <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
            <option value="hoat_dong" @selected(old('trang_thai', $loaiPhongHienTai->trang_thai ?: 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
            <option value="tam_ngung" @selected(old('trang_thai', $loaiPhongHienTai->trang_thai) === 'tam_ngung')>Tạm ngừng</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-6">
        <label class="form-label">Tên loại phòng</label>
        <input
            type="text"
            name="ten_loai_phong"
            class="form-control @error('ten_loai_phong') is-invalid @enderror"
            value="{{ old('ten_loai_phong', $loaiPhongHienTai->ten_loai_phong) }}"
            placeholder="Ví dụ: Deluxe hướng biển, Family Suite"
            required
        >
        @error('ten_loai_phong')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-3">
        <label class="form-label">Giá chuẩn / đêm</label>
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
            placeholder="King, Queen, Twin..."
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

    <div class="col-12">
        <label class="form-label">Tiện ích nổi bật</label>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="feature-toggle">
                    <input type="checkbox" name="co_ban_cong" value="1" @checked(old('co_ban_cong', $loaiPhongHienTai->co_ban_cong))>
                    <span>
                        <strong>Có ban công</strong>
                        <small>Phù hợp cho hạng phòng nghỉ dưỡng hoặc phòng view đẹp.</small>
                    </span>
                </label>
            </div>
            <div class="col-md-4">
                <label class="feature-toggle">
                    <input type="checkbox" name="co_bep_rieng" value="1" @checked(old('co_bep_rieng', $loaiPhongHienTai->co_bep_rieng))>
                    <span>
                        <strong>Có bếp riêng</strong>
                        <small>Hữu ích cho khách ở dài ngày, gia đình hoặc căn hộ dịch vụ.</small>
                    </span>
                </label>
            </div>
            <div class="col-md-4">
                <label class="feature-toggle">
                    <input type="checkbox" name="co_huong_bien" value="1" @checked(old('co_huong_bien', $loaiPhongHienTai->co_huong_bien))>
                    <span>
                        <strong>Có hướng biển</strong>
                        <small>Giúp bán phòng theo trải nghiệm và hỗ trợ upsell hiệu quả.</small>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label">Mô tả</label>
        <textarea
            name="mo_ta"
            rows="4"
            class="form-control @error('mo_ta') is-invalid @enderror"
            placeholder="Mô tả điểm khác biệt của hạng phòng, nội thất và chính sách áp dụng..."
        >{{ old('mo_ta', $loaiPhongHienTai->mo_ta) }}</textarea>
        @error('mo_ta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@once
    @push('styles')
        <style>
            .feature-toggle {
                display: flex;
                gap: 12px;
                align-items: flex-start;
                min-height: 100%;
                border: 1px solid #d7e3ef;
                border-radius: 18px;
                background: #fbfdff;
                padding: 16px;
                cursor: pointer;
            }

            .feature-toggle input {
                margin-top: 5px;
                transform: scale(1.15);
            }

            .feature-toggle strong {
                display: block;
                color: #15314d;
            }

            .feature-toggle small {
                display: block;
                margin-top: 4px;
                color: #6b8298;
                line-height: 1.5;
            }
        </style>
    @endpush
@endonce
