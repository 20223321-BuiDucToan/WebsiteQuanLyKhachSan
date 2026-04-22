@php
    $phongHienTai = $phong ?? null;
    $danhSachAnhPhong = $phongHienTai?->layDanhSachAnhPhong() ?? [];
    $anhDaChonXoa = collect(old('xoa_anh_phong', []))
        ->map(fn ($anh) => (string) $anh)
        ->all();
    $trangThaiPhongHienThi = $phongHienTai
        ? \App\Support\HienThiGiaTri::nhanGiaTri($phongHienTai->trang_thai)
        : 'Tự động tính sau khi lưu';
    $phongDangCoDatPhong = $phongHienTai?->coDatPhongHoatDong() ?? false;
@endphp

<div class="row g-4">
    <div class="col-md-4">
        <label class="form-label">Số phòng</label>
        <input
            type="text"
            name="so_phong"
            class="form-control @error('so_phong') is-invalid @enderror"
            value="{{ old('so_phong', $phongHienTai?->so_phong ?? '') }}"
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
                <option value="{{ $loaiPhong->id }}" @selected((string) old('loai_phong_id', $phongHienTai?->loai_phong_id ?? '') === (string) $loaiPhong->id)>
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
            value="{{ old('tang', $phongHienTai?->tang ?? '') }}"
        >
        @error('tang')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Trạng thái phòng</label>
        <div class="border rounded-4 bg-light px-3 py-3 mb-2">
            <div class="fw-semibold text-dark">{{ $trangThaiPhongHienThi }}</div>
            <div class="form-text mt-2 mb-0">Hệ thống tự động đồng bộ theo tình trạng hoạt động, vệ sinh và các đơn đặt phòng đang hiệu lực.</div>
            @if($phongDangCoDatPhong)
                <div class="small text-warning mt-2">Phòng đang có đơn hiệu lực nên không thể chuyển sang tạm ngưng.</div>
            @endif
        </div>
        <select class="d-none" disabled aria-hidden="true" tabindex="-1">
            <option value="trong" @selected(old('trang_thai', $phongHienTai?->trang_thai ?? 'trong') === 'trong')>Trống</option>
            <option value="da_dat" @selected(old('trang_thai', $phongHienTai?->trang_thai ?? '') === 'da_dat')>Đã đặt</option>
            <option value="dang_su_dung" @selected(old('trang_thai', $phongHienTai?->trang_thai ?? '') === 'dang_su_dung')>Đang sử dụng</option>
            <option value="don_dep" @selected(old('trang_thai', $phongHienTai?->trang_thai ?? '') === 'don_dep')>Dọn dẹp</option>
            <option value="bao_tri" @selected(old('trang_thai', $phongHienTai?->trang_thai ?? '') === 'bao_tri')>Bảo trì</option>
        </select>
        @error('trang_thai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tình trạng vệ sinh</label>
        <select name="tinh_trang_ve_sinh" class="form-select @error('tinh_trang_ve_sinh') is-invalid @enderror" required>
            <option value="sach" @selected(old('tinh_trang_ve_sinh', $phongHienTai?->tinh_trang_ve_sinh ?? 'sach') === 'sach')>Sạch</option>
            <option value="can_don" @selected(old('tinh_trang_ve_sinh', $phongHienTai?->tinh_trang_ve_sinh ?? '') === 'can_don')>Cần dọn</option>
            <option value="dang_don" @selected(old('tinh_trang_ve_sinh', $phongHienTai?->tinh_trang_ve_sinh ?? '') === 'dang_don')>Đang dọn</option>
            <option value="ban" @selected(old('tinh_trang_ve_sinh', $phongHienTai?->tinh_trang_ve_sinh ?? '') === 'ban')>Bẩn</option>
        </select>
        @error('tinh_trang_ve_sinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tình trạng hoạt động</label>
        <select name="tinh_trang_hoat_dong" class="form-select @error('tinh_trang_hoat_dong') is-invalid @enderror" required>
            <option value="hoat_dong" @selected(old('tinh_trang_hoat_dong', $phongHienTai?->tinh_trang_hoat_dong ?? 'hoat_dong') === 'hoat_dong')>Hoạt động</option>
            <option value="tam_ngung" @selected(old('tinh_trang_hoat_dong', $phongHienTai?->tinh_trang_hoat_dong ?? '') === 'tam_ngung')>Tạm ngưng</option>
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
            value="{{ old('gia_mac_dinh', $phongHienTai?->gia_mac_dinh ?? '') }}"
        >
        @error('gia_mac_dinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Ghi chú</label>
        <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu', $phongHienTai?->ghi_chu ?? '') }}</textarea>
        @error('ghi_chu')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Hình ảnh phòng</label>
        <div class="room-upload-box">
            <input
                type="file"
                id="anh_phong"
                name="anh_phong[]"
                class="visually-hidden @error('anh_phong') is-invalid @enderror @error('anh_phong.*') is-invalid @enderror"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                multiple
            >

            <div class="room-upload-toolbar">
                <button type="button" class="btn btn-gradient btn-sm" id="room-upload-select">
                    <i class="fa-regular fa-images me-2"></i>Chọn / thêm nhiều ảnh
                </button>
                <button type="button" class="btn btn-soft btn-sm" id="room-upload-reset" disabled>
                    <i class="fa-solid fa-trash-can me-2"></i>Bỏ danh sách mới
                </button>
            </div>

            <div class="room-upload-summary" id="room-upload-summary">
                Chưa chọn ảnh mới.
            </div>

            <div class="form-text mt-2">
                Bạn có thể chọn nhiều ảnh trong một lần, hoặc bấm nút trên nhiều lần để cộng dồn ảnh trước khi lưu. Mỗi ảnh tối đa 4MB, hỗ trợ JPG, JPEG, PNG, WEBP.
            </div>

            @error('anh_phong')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            @error('anh_phong.*')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if($danhSachAnhPhong !== [])
        <div class="col-12">
            <label class="form-label">Ảnh hiện có</label>
            <div class="room-image-grid">
                @foreach($danhSachAnhPhong as $anhPhong)
                    <label class="existing-room-image {{ in_array($anhPhong, $anhDaChonXoa, true) ? 'is-selected' : '' }}">
                        <img src="{{ asset($anhPhong) }}" alt="Ảnh phòng {{ $phongHienTai?->so_phong }}">
                        <span class="existing-room-image__overlay">
                            <span class="form-check mb-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="xoa_anh_phong[]"
                                    value="{{ $anhPhong }}"
                                    @checked(in_array($anhPhong, $anhDaChonXoa, true))
                                >
                                <span class="form-check-label">Xóa ảnh này</span>
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            <div class="form-text mt-2">Chọn ảnh cần gỡ khỏi phòng rồi bấm lưu thay đổi.</div>
        </div>
    @endif

    <div class="col-12">
        <label class="form-label">Ảnh mới đã chọn</label>
        <div class="room-preview-panel" id="room-upload-preview" data-empty-text="Chưa chọn ảnh mới. Ảnh bạn chọn sẽ hiện ở đây để kiểm tra trước khi lưu.">
            <div class="room-preview-empty">
                <i class="fa-regular fa-images me-2"></i>Chưa chọn ảnh mới. Ảnh bạn chọn sẽ hiện ở đây để kiểm tra trước khi lưu.
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .room-upload-box {
                border: 1px dashed #b8c9dc;
                border-radius: 18px;
                background: linear-gradient(180deg, #fbfdff, #f5f9fe);
                padding: 16px;
            }

            .room-upload-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
            }

            .room-upload-summary {
                margin-top: 12px;
                color: #24415f;
                font-weight: 600;
            }

            .room-image-grid,
            .room-preview-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
                gap: 14px;
            }

            .existing-room-image,
            .room-preview-card {
                position: relative;
                overflow: hidden;
                border: 1px solid #d7e3ef;
                border-radius: 18px;
                background: #fff;
                min-height: 170px;
            }

            .existing-room-image {
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .existing-room-image:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 26px rgba(15, 41, 68, 0.1);
            }

            .existing-room-image.is-selected {
                border-color: #dc2626;
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
            }

            .existing-room-image img,
            .room-preview-card img {
                width: 100%;
                height: 170px;
                object-fit: cover;
                display: block;
            }

            .existing-room-image__overlay {
                position: absolute;
                inset: auto 0 0;
                padding: 12px;
                color: #fff;
                background: linear-gradient(180deg, rgba(10, 22, 37, 0) 0%, rgba(10, 22, 37, 0.78) 100%);
            }

            .existing-room-image .form-check-input {
                margin-top: 0.15rem;
                cursor: pointer;
            }

            .existing-room-image .form-check-label {
                margin-left: 0.35rem;
                font-size: 0.92rem;
                font-weight: 600;
            }

            .room-preview-panel {
                border: 1px dashed #c8d7e7;
                border-radius: 18px;
                background: #fbfdff;
                padding: 16px;
            }

            .room-preview-empty {
                color: #6b7f95;
                text-align: center;
                padding: 20px 12px;
            }

            .room-preview-card__meta {
                padding: 10px 12px 12px;
            }

            .room-preview-card__name {
                display: block;
                font-weight: 700;
                color: #18324d;
                word-break: break-word;
            }

            .room-preview-card__size {
                display: block;
                color: #6b7f95;
                font-size: 0.86rem;
                margin-top: 4px;
            }

            .room-preview-card__remove {
                position: absolute;
                top: 10px;
                right: 10px;
                border: 0;
                border-radius: 999px;
                padding: 7px 10px;
                color: #fff;
                font-size: 0.8rem;
                font-weight: 700;
                background: rgba(190, 18, 60, 0.92);
                box-shadow: 0 10px 20px rgba(10, 22, 37, 0.2);
            }

            .room-preview-card__remove:hover {
                background: #be123c;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            const roomImageInput = document.getElementById('anh_phong');
            const roomUploadSelectButton = document.getElementById('room-upload-select');
            const roomUploadResetButton = document.getElementById('room-upload-reset');
            const roomUploadSummary = document.getElementById('room-upload-summary');
            const roomUploadPreview = document.getElementById('room-upload-preview');
            const existingRoomImageToggles = document.querySelectorAll('.existing-room-image input[type="checkbox"]');
            let roomPendingTransfer = typeof DataTransfer !== 'undefined' ? new DataTransfer() : null;

            function dinhDangDungLuongTep(bytes) {
                if (!Number.isFinite(bytes) || bytes <= 0) {
                    return '0 KB';
                }

                const kilobytes = bytes / 1024;

                if (kilobytes < 1024) {
                    return kilobytes.toFixed(0) + ' KB';
                }

                return (kilobytes / 1024).toFixed(2) + ' MB';
            }

            function taoKhoaTep(tep) {
                return [tep.name, tep.size, tep.lastModified, tep.type].join('::');
            }

            function layDanhSachTepDaChon() {
                if (!roomImageInput) {
                    return [];
                }

                if (roomPendingTransfer) {
                    return Array.from(roomPendingTransfer.files);
                }

                return Array.from(roomImageInput.files ?? []);
            }

            function capNhatInputAnhPhong() {
                if (roomPendingTransfer && roomImageInput) {
                    roomImageInput.files = roomPendingTransfer.files;
                }
            }

            function capNhatTomTatAnhPhong() {
                const tongSoTep = layDanhSachTepDaChon().length;

                if (roomUploadSummary) {
                    roomUploadSummary.textContent = tongSoTep > 0
                        ? 'Đã chọn ' + tongSoTep + ' ảnh mới. Bạn vẫn có thể bấm nút trên để thêm ảnh trước khi lưu.'
                        : 'Chưa chọn ảnh mới.';
                }

                if (roomUploadResetButton) {
                    roomUploadResetButton.disabled = tongSoTep === 0;
                }
            }

            function xoaTatCaAnhMoi() {
                if (roomPendingTransfer) {
                    roomPendingTransfer = new DataTransfer();
                    capNhatInputAnhPhong();
                }

                if (roomImageInput) {
                    roomImageInput.value = '';
                }

                hienThiPreviewAnhPhong();
            }

            function xoaMotAnhMoi(indexCanXoa) {
                if (!roomPendingTransfer) {
                    xoaTatCaAnhMoi();
                    return;
                }

                const danhSachConLai = Array.from(roomPendingTransfer.files).filter((_, index) => index !== indexCanXoa);
                roomPendingTransfer = new DataTransfer();
                danhSachConLai.forEach((tep) => roomPendingTransfer.items.add(tep));
                capNhatInputAnhPhong();
                hienThiPreviewAnhPhong();
            }

            function hienThiPreviewAnhPhong() {
                if (!roomUploadPreview) {
                    return;
                }

                const danhSachTep = layDanhSachTepDaChon();

                if (danhSachTep.length === 0) {
                    roomUploadPreview.innerHTML = '<div class="room-preview-empty"><i class="fa-regular fa-images me-2"></i>' + roomUploadPreview.dataset.emptyText + '</div>';
                    capNhatTomTatAnhPhong();
                    return;
                }

                const khung = document.createElement('div');
                khung.className = 'room-preview-grid';

                danhSachTep.forEach((tep, index) => {
                    const theAnh = document.createElement('img');
                    theAnh.src = URL.createObjectURL(tep);
                    theAnh.alt = tep.name;
                    theAnh.addEventListener('load', () => URL.revokeObjectURL(theAnh.src), { once: true });

                    const nutXoa = document.createElement('button');
                    nutXoa.type = 'button';
                    nutXoa.className = 'room-preview-card__remove';
                    nutXoa.textContent = 'Xóa';
                    nutXoa.addEventListener('click', () => xoaMotAnhMoi(index));

                    const tieuDe = document.createElement('span');
                    tieuDe.className = 'room-preview-card__name';
                    tieuDe.textContent = tep.name;

                    const dungLuong = document.createElement('span');
                    dungLuong.className = 'room-preview-card__size';
                    dungLuong.textContent = dinhDangDungLuongTep(tep.size);

                    const thongTin = document.createElement('div');
                    thongTin.className = 'room-preview-card__meta';
                    thongTin.appendChild(tieuDe);
                    thongTin.appendChild(dungLuong);

                    const the = document.createElement('div');
                    the.className = 'room-preview-card';
                    the.appendChild(theAnh);
                    the.appendChild(nutXoa);
                    the.appendChild(thongTin);

                    khung.appendChild(the);
                });

                roomUploadPreview.innerHTML = '';
                roomUploadPreview.appendChild(khung);
                capNhatTomTatAnhPhong();
            }

            roomUploadSelectButton?.addEventListener('click', () => {
                roomImageInput?.click();
            });

            roomUploadResetButton?.addEventListener('click', xoaTatCaAnhMoi);

            roomImageInput?.addEventListener('change', () => {
                const danhSachTepMoi = Array.from(roomImageInput.files ?? []);

                if (danhSachTepMoi.length === 0) {
                    hienThiPreviewAnhPhong();
                    return;
                }

                if (roomPendingTransfer) {
                    const danhSachDaCo = new Set(Array.from(roomPendingTransfer.files).map(taoKhoaTep));

                    danhSachTepMoi.forEach((tep) => {
                        const khoaTep = taoKhoaTep(tep);

                        if (!danhSachDaCo.has(khoaTep)) {
                            roomPendingTransfer.items.add(tep);
                            danhSachDaCo.add(khoaTep);
                        }
                    });

                    capNhatInputAnhPhong();
                }

                hienThiPreviewAnhPhong();
            });

            hienThiPreviewAnhPhong();

            existingRoomImageToggles.forEach((checkbox) => {
                const theAnh = checkbox.closest('.existing-room-image');
                const dongBoTrangThai = () => theAnh?.classList.toggle('is-selected', checkbox.checked);

                checkbox.addEventListener('change', dongBoTrangThai);
                dongBoTrangThai();
            });
        </script>
    @endpush
@endonce
