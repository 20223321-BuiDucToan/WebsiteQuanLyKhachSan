@extends('layouts.admin')

@section('title', 'Tạo đơn đặt phòng')

@push('styles')
    <style>
        .booking-create-hero {
<<<<<<< HEAD
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
=======
            border: none;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.22), transparent 34%),
                linear-gradient(135deg, #173652, #2563eb 55%, #38bdf8);
            color: #fff;
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .booking-create-hero .section-title,
        .booking-create-hero .section-subtitle {
<<<<<<< HEAD
            color: inherit;
        }

        .booking-create-hero .section-subtitle {
            opacity: 1;
=======
            color: #fff;
        }

        .booking-create-hero .section-subtitle {
            opacity: 0.82;
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .hero-stat-card {
            border-radius: 18px;
            padding: 16px;
<<<<<<< HEAD
            background: #f8fbff;
            border: 1px solid #dbe7f2;
=======
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
<<<<<<< HEAD
            color: #6b8298;
=======
            opacity: 0.84;
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.45rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
<<<<<<< HEAD
            color: #6b8298;
=======
            opacity: 0.78;
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .section-block {
            border: 1px solid #e5edf6;
            border-radius: 20px;
            padding: 20px;
            background: #fbfdff;
        }

        .section-block-title {
            font-size: 1rem;
            font-weight: 800;
            color: #173652;
            margin-bottom: 4px;
        }

        .section-block-subtitle {
            color: #68839f;
            font-size: 0.86rem;
            margin-bottom: 16px;
        }

        .preview-card {
            position: sticky;
            top: 94px;
        }

        .preview-panel {
            border-radius: 22px;
<<<<<<< HEAD
            background: #fff;
=======
            background: linear-gradient(180deg, #f8fbff, #ffffff);
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
            border: 1px solid #dbe7f2;
            padding: 22px;
        }

        .preview-room {
            border-radius: 18px;
            padding: 16px;
            background: #eef6ff;
            border: 1px solid #d6e5f3;
        }

        .preview-label {
            font-size: 0.82rem;
            color: #68839f;
            margin-bottom: 4px;
        }

        .preview-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #173652;
        }

        .preview-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .preview-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #edf2f8;
        }

        .preview-item:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .helper-list {
<<<<<<< HEAD
            display: none;
=======
            display: grid;
            gap: 10px;
            margin-top: 18px;
>>>>>>> 8e80bbc81bba78f78f2e090ea3984d8c0db04b6e
        }

        .helper-item {
            display: flex;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #e5edf6;
            color: #35516d;
        }

        .helper-item i {
            margin-top: 3px;
            color: #2563eb;
        }

        .capacity-alert {
            margin-top: 14px;
            border-radius: 16px;
            padding: 12px 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 0.86rem;
            display: none;
        }

        @media (max-width: 1199px) {
            .preview-card {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <div class="premium-card booking-create-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Tạo đơn đặt phòng</h2>
                    <p class="section-subtitle">Tạo nhanh đơn nội bộ, kiểm tra sức chứa và theo dõi chi phí tạm tính ngay trong lúc nhập.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dat-phong.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-list me-2"></i>Quay lại danh sách
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Phòng hoạt động</div>
                    <div class="hero-stat-value">{{ $thongKePhong['tong_hoat_dong'] }}</div>
                    <div class="hero-stat-note">{{ $thongKePhong['san_sang_hom_nay'] }} phòng đang sẵn sàng ngay</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Đang sử dụng</div>
                    <div class="hero-stat-value">{{ $thongKePhong['dang_su_dung'] }}</div>
                    <div class="hero-stat-note">Vẫn có thể đặt cho lịch tương lai nếu không xung đột</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Giá trung bình</div>
                    <div class="hero-stat-value">{{ number_format((float) $thongKePhong['gia_trung_binh'], 0, ',', '.') }}</div>
                    <div class="hero-stat-note">VNĐ mỗi đêm trên tập phòng đang hoạt động</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('dat-phong.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="premium-card">
                    <div class="card-body p-4 d-grid gap-4">
                        <div class="section-block">
                            <div class="section-block-title">Thông tin khách hàng</div>
                            <div class="section-block-subtitle">Bắt buộc có ít nhất số điện thoại hoặc email để liên hệ xác nhận.</div>

                            <div class="row g-3">
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
                            </div>
                        </div>

                        <div class="section-block">
                            <div class="section-block-title">Thông tin lưu trú</div>
                            <div class="section-block-subtitle">Chọn phòng, lịch nhận trả và số lượng khách để hệ thống ước tính nhanh chi phí.</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phòng</label>
                                    <select name="phong_id" id="phong_id" class="form-select @error('phong_id') is-invalid @enderror" required>
                                        <option value="">-- Chọn phòng --</option>
                                        @foreach($danhSachPhong as $phong)
                                            @php
                                                $gia = (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong?->gia_mot_dem ?? 0);
                                                $sucChua = (int) ($phong->loaiPhong?->so_nguoi_toi_da ?? 1);
                                            @endphp
                                            <option
                                                value="{{ $phong->id }}"
                                                data-so-phong="{{ $phong->so_phong }}"
                                                data-loai="{{ $phong->loaiPhong?->ten_loai_phong ?? 'Không rõ loại' }}"
                                                data-gia="{{ $gia }}"
                                                data-suc-chua="{{ $sucChua }}"
                                                data-tang="{{ $phong->tang ?? '-' }}"
                                                data-trang-thai="{{ \App\Support\HienThiGiaTri::nhanGiaTri($phong->trang_thai) }}"
                                                data-dien-tich="{{ $phong->loaiPhong?->dien_tich ? number_format((float) $phong->loaiPhong->dien_tich, 0, ',', '.') . ' m²' : 'Chưa cập nhật' }}"
                                                data-giuong="{{ $phong->loaiPhong?->so_giuong ? $phong->loaiPhong->so_giuong . ' giường' : 'Chưa cập nhật' }}"
                                                @selected((string) old('phong_id') === (string) $phong->id)
                                            >
                                                Phòng {{ $phong->so_phong }} - {{ $phong->loaiPhong?->ten_loai_phong ?? 'Không rõ loại' }}
                                                @if($gia)
                                                    ({{ number_format($gia, 0, ',', '.') }} VNĐ/đêm)
                                                @endif
                                                - tối đa {{ $sucChua }} khách
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('phong_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Ngày nhận</label>
                                    <input type="date" id="ngay_nhan" name="ngay_nhan" class="form-control @error('ngay_nhan') is-invalid @enderror" value="{{ old('ngay_nhan', now()->toDateString()) }}" required>
                                    @error('ngay_nhan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Ngày trả</label>
                                    <input type="date" id="ngay_tra" name="ngay_tra" class="form-control @error('ngay_tra') is-invalid @enderror" value="{{ old('ngay_tra', now()->addDay()->toDateString()) }}" required>
                                    @error('ngay_tra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Người lớn</label>
                                    <input type="number" id="so_nguoi_lon" name="so_nguoi_lon" min="1" max="20" class="form-control @error('so_nguoi_lon') is-invalid @enderror" value="{{ old('so_nguoi_lon', 1) }}" required>
                                    @error('so_nguoi_lon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Trẻ em</label>
                                    <input type="number" id="so_tre_em" name="so_tre_em" min="0" max="20" class="form-control @error('so_tre_em') is-invalid @enderror" value="{{ old('so_tre_em', 0) }}">
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
                            </div>
                        </div>

                        <div class="section-block">
                            <div class="section-block-title">Yêu cầu và ghi chú</div>
                            <div class="section-block-subtitle">Ghi lại nhu cầu đặc biệt của khách và lưu ý nội bộ cho ca làm việc tiếp theo.</div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Yêu cầu đặc biệt</label>
                                    <input type="text" name="yeu_cau_dac_biet" class="form-control @error('yeu_cau_dac_biet') is-invalid @enderror" value="{{ old('yeu_cau_dac_biet') }}" placeholder="Ví dụ: phòng không hút thuốc, giường đôi...">
                                    @error('yeu_cau_dac_biet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Ghi chú nội bộ</label>
                                    <textarea name="ghi_chu" rows="3" class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                                    @error('ghi_chu')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-gradient"><i class="fa-solid fa-floppy-disk me-2"></i>Lưu đơn đặt phòng</button>
                            <a href="{{ route('dat-phong.index') }}" class="btn btn-soft">Quay lại</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="preview-card">
                    <div class="preview-panel">
                        <div class="preview-room">
                            <div class="preview-label">Phòng đang chọn</div>
                            <div class="preview-value" id="preview-room">Chưa chọn phòng</div>
                            <div class="table-subtext mt-2" id="preview-room-meta">Chọn một phòng để xem chi tiết.</div>
                        </div>

                        <div class="preview-list">
                            <div class="preview-item">
                                <div>
                                    <div class="fw-semibold">Loại phòng</div>
                                    <div class="table-subtext">Loại và vị trí</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" id="preview-type">-</div>
                                    <div class="table-subtext" id="preview-floor">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <div>
                                    <div class="fw-semibold">Sức chứa</div>
                                    <div class="table-subtext">Khả năng tiếp nhận</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" id="preview-capacity">-</div>
                                    <div class="table-subtext" id="preview-guest-count">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <div>
                                    <div class="fw-semibold">Lưu trú</div>
                                    <div class="table-subtext">Số đêm dự kiến</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" id="preview-nights">1 đêm</div>
                                    <div class="table-subtext" id="preview-dates">-</div>
                                </div>
                            </div>
                            <div class="preview-item">
                                <div>
                                    <div class="fw-semibold">Tạm tính</div>
                                    <div class="table-subtext">Giá phòng chưa gồm dịch vụ</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary" id="preview-total">0 VNĐ</div>
                                    <div class="table-subtext" id="preview-price-night">0 VNĐ/đêm</div>
                                </div>
                            </div>
                        </div>

                        <div class="capacity-alert" id="capacity-alert">
                            Số khách hiện tại đang vượt quá sức chứa của phòng đã chọn.
                        </div>

                        <div class="helper-list">
                            <div class="helper-item">
                                <i class="fa-solid fa-bed"></i>
                                <div id="preview-bed">Thông tin giường sẽ hiện tại đây.</div>
                            </div>
                            <div class="helper-item">
                                <i class="fa-solid fa-ruler-combined"></i>
                                <div id="preview-area">Thông tin diện tích sẽ hiện tại đây.</div>
                            </div>
                            <div class="helper-item">
                                <i class="fa-solid fa-circle-info"></i>
                                <div id="preview-status">Trạng thái hiện tại của phòng sẽ hiển thị ở đây.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    @parent
    <script>
        const phongSelect = document.getElementById('phong_id');
        const ngayNhanInput = document.getElementById('ngay_nhan');
        const ngayTraInput = document.getElementById('ngay_tra');
        const nguoiLonInput = document.getElementById('so_nguoi_lon');
        const treEmInput = document.getElementById('so_tre_em');

        const previewRoom = document.getElementById('preview-room');
        const previewRoomMeta = document.getElementById('preview-room-meta');
        const previewType = document.getElementById('preview-type');
        const previewFloor = document.getElementById('preview-floor');
        const previewCapacity = document.getElementById('preview-capacity');
        const previewGuestCount = document.getElementById('preview-guest-count');
        const previewNights = document.getElementById('preview-nights');
        const previewDates = document.getElementById('preview-dates');
        const previewTotal = document.getElementById('preview-total');
        const previewPriceNight = document.getElementById('preview-price-night');
        const previewBed = document.getElementById('preview-bed');
        const previewArea = document.getElementById('preview-area');
        const previewStatus = document.getElementById('preview-status');
        const capacityAlert = document.getElementById('capacity-alert');

        function dinhDangTien(giaTri) {
            return new Intl.NumberFormat('vi-VN').format(giaTri) + ' VNĐ';
        }

        function tinhSoDem() {
            if (!ngayNhanInput?.value || !ngayTraInput?.value) {
                return 1;
            }

            const ngayNhan = new Date(ngayNhanInput.value);
            const ngayTra = new Date(ngayTraInput.value);
            const hieuSo = ngayTra.getTime() - ngayNhan.getTime();
            const soDem = Math.ceil(hieuSo / (1000 * 60 * 60 * 24));

            return soDem > 0 ? soDem : 1;
        }

        function capNhatPreview() {
            const selectedOption = phongSelect?.selectedOptions?.[0];
            const gia = Number(selectedOption?.dataset?.gia || 0);
            const sucChua = Number(selectedOption?.dataset?.sucChua || 0);
            const tongKhach = Number(nguoiLonInput?.value || 0) + Number(treEmInput?.value || 0);
            const soDem = tinhSoDem();
            const tongTien = gia * soDem;

            if (selectedOption && selectedOption.value) {
                previewRoom.textContent = 'Phòng ' + (selectedOption.dataset.soPhong || '-');
                previewRoomMeta.textContent = (selectedOption.dataset.loai || '-') + ' • ' + (selectedOption.dataset.trangThai || '-');
                previewType.textContent = selectedOption.dataset.loai || '-';
                previewFloor.textContent = 'Tầng ' + (selectedOption.dataset.tang || '-');
                previewCapacity.textContent = (sucChua || '-') + ' khách';
                previewBed.textContent = selectedOption.dataset.giuong || 'Chưa cập nhật cấu hình giường.';
                previewArea.textContent = selectedOption.dataset.dienTich || 'Chưa cập nhật diện tích.';
                previewStatus.textContent = 'Trạng thái hiện tại: ' + (selectedOption.dataset.trangThai || '-');
            } else {
                previewRoom.textContent = 'Chưa chọn phòng';
                previewRoomMeta.textContent = 'Chọn một phòng để xem chi tiết.';
                previewType.textContent = '-';
                previewFloor.textContent = '-';
                previewCapacity.textContent = '-';
                previewBed.textContent = 'Thông tin giường sẽ hiện tại đây.';
                previewArea.textContent = 'Thông tin diện tích sẽ hiện tại đây.';
                previewStatus.textContent = 'Trạng thái hiện tại của phòng sẽ hiển thị ở đây.';
            }

            previewGuestCount.textContent = tongKhach + ' khách';
            previewNights.textContent = soDem + ' đêm';
            previewDates.textContent = (ngayNhanInput?.value || '-') + ' → ' + (ngayTraInput?.value || '-');
            previewTotal.textContent = dinhDangTien(tongTien);
            previewPriceNight.textContent = dinhDangTien(gia) + '/đêm';

            if (sucChua > 0 && tongKhach > sucChua) {
                capacityAlert.style.display = 'block';
            } else {
                capacityAlert.style.display = 'none';
            }
        }

        [phongSelect, ngayNhanInput, ngayTraInput, nguoiLonInput, treEmInput].forEach((element) => {
            element?.addEventListener('input', capNhatPreview);
            element?.addEventListener('change', capNhatPreview);
        });

        capNhatPreview();
    </script>
@endsection
