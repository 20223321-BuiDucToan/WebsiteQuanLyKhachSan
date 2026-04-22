@extends('layouts.admin')

@section('title', 'Quản lý thanh toán')

@push('styles')
    <style>
        .payment-hero {
            border: 1px solid #e5edf6;
            background: #fff;
            color: #173652;
        }

        .payment-hero .section-title,
        .payment-hero .section-subtitle {
            color: inherit;
        }

        .payment-hero .section-subtitle {
            opacity: 1;
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
            background: #f8fbff;
            border: 1px solid #dbe7f2;
        }

        .hero-stat-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b8298;
        }

        .hero-stat-value {
            margin-top: 6px;
            font-size: 1.55rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-stat-note {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #6b8298;
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
            margin-bottom: 18px;
        }

        .active-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef8f6;
            color: #0f5f58;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .payment-code {
            font-size: 0.98rem;
            font-weight: 700;
            color: #12304d;
        }

        .table-subtext {
            color: #6b8298;
            font-size: 0.83rem;
        }

        .empty-state {
            border: 1px dashed #cad8e6;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: #68839f;
            background: #fbfdff;
        }

        .priority-queue {
            border: 1px solid #f7d7aa;
            background:
                radial-gradient(circle at top right, rgba(251, 191, 36, 0.18), transparent 28%),
                linear-gradient(180deg, #fffdf8, #ffffff);
        }

        .request-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .request-card {
            border: 1px solid #f4dfb2;
            border-radius: 20px;
            padding: 18px;
            background: #fff;
            box-shadow: 0 14px 28px rgba(138, 92, 14, 0.06);
        }

        .request-card__meta {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .request-card__label {
            color: #876544;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .request-card__value {
            color: #173652;
            font-weight: 700;
        }

        .request-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }
    </style>
@endpush

@section('content')
    @php
        $coBoLoc = filled($tuKhoa) || filled($trangThai) || filled($phuongThuc) || filled($nguonTao) || filled($tuNgay) || filled($denNgay);
        $mapTrangThai = [
            'thanh_cong' => 'chip chip-success',
            'cho_xu_ly' => 'chip chip-warning',
            'that_bai' => 'chip chip-danger',
        ];
    @endphp

    <div class="premium-card payment-hero">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="section-title">Quản lý thanh toán</h2>
                    <p class="section-subtitle">Ghi nhận giao dịch, theo dõi tình trạng xử lý và đối soát nhanh theo hóa đơn.</p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('hoa-don.index') }}" class="btn btn-light fw-semibold">
                        <i class="fa-solid fa-file-invoice me-2"></i>Xem hóa đơn
                    </a>
                    <a
                        href="{{ route('thanh-toan.index', ['trang_thai' => 'cho_xu_ly', 'nguon_tao' => 'khach_hang']) }}"
                        class="btn btn-gradient"
                    >
                        <i class="fa-solid fa-clock me-2"></i>Mở hàng đợi chờ duyệt
                    </a>
                </div>
            </div>

            <div class="hero-stat-grid">
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Tổng giao dịch</div>
                    <div class="hero-stat-value">{{ $thongKe['tong_giao_dich'] }}</div>
                    <div class="hero-stat-note">{{ $danhSachHoaDon->count() }} hóa đơn đang chờ thu trong danh sách chọn nhanh</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Thành công</div>
                    <div class="hero-stat-value">{{ $thongKe['thanh_cong'] }}</div>
                    <div class="hero-stat-note">{{ number_format((float) $thongKe['tong_tien_thanh_cong'], 0, ',', '.') }} VNĐ đã ghi nhận</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Chờ xử lý</div>
                    <div class="hero-stat-value text-warning">{{ $thongKe['cho_xu_ly'] }}</div>
                    <div class="hero-stat-note">{{ $thongKe['yeu_cau_khach_hang'] }} giao dịch đến từ khách hàng cần đối soát</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-label">Chờ duyệt từ khách</div>
                    <div class="hero-stat-value text-danger">{{ number_format((float) $tongTienChoDuyetKhachHang, 0, ',', '.') }}</div>
                    <div class="hero-stat-note">VNĐ đang ở trạng thái chờ xác nhận nội bộ</div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card priority-queue">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <div class="section-block-title">Yêu cầu thanh toán khách hàng chờ xác nhận</div>
                    <div class="section-block-subtitle">Ngay khi khách gửi thanh toán, yêu cầu sẽ xuất hiện tại đây để admin hoặc nhân viên duyệt thành công hoặc từ chối.</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-warning">{{ $danhSachYeuCauKhachHangChoXuLy->count() }} yêu cầu đang chờ</div>
                    <div class="small text-muted">Tổng số tiền chờ xác nhận: {{ number_format((float) $tongTienChoDuyetKhachHang, 0, ',', '.') }} VNĐ</div>
                </div>
            </div>

            @if($danhSachYeuCauKhachHangChoXuLy->isEmpty())
                <div class="empty-state">Hiện tại không có yêu cầu thanh toán nào từ khách hàng đang chờ xử lý.</div>
            @else
                <div class="request-grid">
                    @foreach($danhSachYeuCauKhachHangChoXuLy as $thanhToan)
                        <div class="request-card">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="payment-code">{{ $thanhToan->ma_thanh_toan }}</div>
                                    <div class="table-subtext">{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }} - {{ $thanhToan->hoaDon?->datPhong?->ma_dat_phong ?? '-' }}</div>
                                </div>
                                <span class="chip chip-warning">Chờ xử lý</span>
                            </div>

                            <div class="request-card__meta">
                                <div>
                                    <div class="request-card__label">Khách hàng</div>
                                    <div class="request-card__value">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->ho_ten ?? '-' }}</div>
                                    <div class="table-subtext">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->so_dien_thoai ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="request-card__label">Số tiền và phương thức</div>
                                    <div class="request-card__value text-danger">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</div>
                                    <div class="table-subtext">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->phuong_thuc_thanh_toan) }}</div>
                                </div>
                                <div>
                                    <div class="request-card__label">Tham chiếu</div>
                                    <div class="request-card__value">{{ $thanhToan->ma_tham_chieu ?: 'Chưa có mã tham chiếu' }}</div>
                                </div>
                                <div>
                                    <div class="request-card__label">Thời điểm gửi</div>
                                    <div class="request-card__value">{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</div>
                                </div>
                                <div>
                                    <div class="request-card__label">Ghi chú</div>
                                    <div class="table-subtext">{{ $thanhToan->ghi_chu ?: 'Không có ghi chú bổ sung.' }}</div>
                                </div>
                            </div>

                            <div class="request-card__actions">
                                <form method="POST" action="{{ route('thanh-toan.cap-nhat-trang-thai', $thanhToan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="trang_thai" value="thanh_cong">
                                    <button class="btn btn-outline-success btn-sm">Duyệt thanh toán</button>
                                </form>

                                <form method="POST" action="{{ route('thanh-toan.cap-nhat-trang-thai', $thanhToan) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="trang_thai" value="that_bai">
                                    <button class="btn btn-outline-danger btn-sm">Từ chối</button>
                                </form>

                                @if($thanhToan->hoaDon)
                                    <a href="{{ route('hoa-don.show', $thanhToan->hoaDon) }}" class="btn btn-soft btn-sm">
                                        Mở hóa đơn
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="section-block-title">Ghi nhận giao dịch nội bộ</div>
            <div class="section-block-subtitle">Dùng khi thu trực tiếp tại quầy hoặc khi nhân viên cần nhập giao dịch thủ công.</div>

            <form action="{{ route('thanh-toan.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-lg-4">
                    <label class="form-label">Hóa đơn cần thu</label>
                    <select name="hoa_don_id" class="form-select @error('hoa_don_id') is-invalid @enderror" required>
                        <option value="">-- Chọn hóa đơn --</option>
                        @foreach($danhSachHoaDon as $hoaDon)
                            <option value="{{ $hoaDon->id }}" @selected((string) old('hoa_don_id') === (string) $hoaDon->id)>
                                {{ $hoaDon->ma_hoa_don }} - {{ number_format((float) $hoaDon->tong_tien, 0, ',', '.') }} VNĐ
                            </option>
                        @endforeach
                    </select>
                    @error('hoa_don_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Số tiền</label>
                    <input type="number" min="1000" step="1000" name="so_tien" class="form-control @error('so_tien') is-invalid @enderror" value="{{ old('so_tien') }}" required>
                    @error('so_tien')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Phương thức</label>
                    <select name="phuong_thuc_thanh_toan" class="form-select @error('phuong_thuc_thanh_toan') is-invalid @enderror" required>
                        <option value="tien_mat" @selected(old('phuong_thuc_thanh_toan', 'tien_mat') === 'tien_mat')>Tiền mặt</option>
                        <option value="chuyen_khoan" @selected(old('phuong_thuc_thanh_toan') === 'chuyen_khoan')>Chuyển khoản</option>
                        <option value="the" @selected(old('phuong_thuc_thanh_toan') === 'the')>Thẻ</option>
                        <option value="vi_dien_tu" @selected(old('phuong_thuc_thanh_toan') === 'vi_dien_tu')>Ví điện tử</option>
                        <option value="khac" @selected(old('phuong_thuc_thanh_toan') === 'khac')>Khác</option>
                    </select>
                    @error('phuong_thuc_thanh_toan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Mã tham chiếu</label>
                    <input type="text" name="ma_tham_chieu" class="form-control @error('ma_tham_chieu') is-invalid @enderror" value="{{ old('ma_tham_chieu') }}" placeholder="Nếu có">
                    @error('ma_tham_chieu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror" required>
                        <option value="thanh_cong" @selected(old('trang_thai', 'thanh_cong') === 'thanh_cong')>Thành công</option>
                        <option value="cho_xu_ly" @selected(old('trang_thai') === 'cho_xu_ly')>Chờ xử lý</option>
                        <option value="that_bai" @selected(old('trang_thai') === 'that_bai')>Thất bại</option>
                    </select>
                    @error('trang_thai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Thời điểm</label>
                    <input type="datetime-local" name="thoi_diem_thanh_toan" class="form-control @error('thoi_diem_thanh_toan') is-invalid @enderror" value="{{ old('thoi_diem_thanh_toan', now()->format('Y-m-d\\TH:i')) }}">
                    @error('thoi_diem_thanh_toan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <input type="text" name="ghi_chu" class="form-control @error('ghi_chu') is-invalid @enderror" value="{{ old('ghi_chu') }}" placeholder="Nội dung bổ sung nếu cần">
                    @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-gradient" type="submit">
                        <i class="fa-solid fa-money-check-dollar me-2"></i>Ghi nhận thanh toán
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="section-block-title">Bộ lọc giao dịch</div>
            <div class="section-block-subtitle">Tìm nhanh theo mã giao dịch, hóa đơn, khách hàng, phương thức và khoảng thời gian.</div>

            <form method="GET" class="row g-3">
                <div class="col-xl-4">
                    <label class="form-label">Từ khóa</label>
                    <input type="text" name="tu_khoa" class="form-control" value="{{ $tuKhoa }}" placeholder="Mã giao dịch, mã hóa đơn, tên khách">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="thanh_cong" @selected($trangThai === 'thanh_cong')>Thành công</option>
                        <option value="cho_xu_ly" @selected($trangThai === 'cho_xu_ly')>Chờ xử lý</option>
                        <option value="that_bai" @selected($trangThai === 'that_bai')>Thất bại</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Phương thức</label>
                    <select name="phuong_thuc" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="tien_mat" @selected($phuongThuc === 'tien_mat')>Tiền mặt</option>
                        <option value="chuyen_khoan" @selected($phuongThuc === 'chuyen_khoan')>Chuyển khoản</option>
                        <option value="the" @selected($phuongThuc === 'the')>Thẻ</option>
                        <option value="vi_dien_tu" @selected($phuongThuc === 'vi_dien_tu')>Ví điện tử</option>
                        <option value="khac" @selected($phuongThuc === 'khac')>Khác</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Nguồn tạo</label>
                    <select name="nguon_tao" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="noi_bo" @selected($nguonTao === 'noi_bo')>Nội bộ</option>
                        <option value="khach_hang" @selected($nguonTao === 'khach_hang')>Khách hàng</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tu_ngay" class="form-control" value="{{ $tuNgay }}">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="den_ngay" class="form-control" value="{{ $denNgay }}">
                </div>

                <div class="col-xl-2 col-md-6 d-flex align-items-end">
                    <button class="btn btn-gradient w-100">Lọc</button>
                </div>

                <div class="col-xl-2 col-md-6 d-flex align-items-end">
                    <a href="{{ route('thanh-toan.index') }}" class="btn btn-soft w-100">Đặt lại</a>
                </div>
            </form>

            @if($coBoLoc)
                <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                    @if($tuKhoa)
                        <span class="active-filter-chip"><i class="fa-solid fa-magnifying-glass"></i>{{ $tuKhoa }}</span>
                    @endif
                    @if($trangThai)
                        <span class="active-filter-chip"><i class="fa-solid fa-circle-dot"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($trangThai) }}</span>
                    @endif
                    @if($phuongThuc)
                        <span class="active-filter-chip"><i class="fa-solid fa-credit-card"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($phuongThuc) }}</span>
                    @endif
                    @if($nguonTao)
                        <span class="active-filter-chip"><i class="fa-solid fa-user-check"></i>{{ \App\Support\HienThiGiaTri::nhanGiaTri($nguonTao) }}</span>
                    @endif
                    @if($tuNgay || $denNgay)
                        <span class="active-filter-chip"><i class="fa-regular fa-calendar"></i>{{ $tuNgay ?: '...' }} - {{ $denNgay ?: '...' }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="premium-card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Danh sách giao dịch</h5>
                    <div class="text-muted small">
                        {{ $thongKe['tong_giao_dich'] }} giao dịch trong bộ lọc hiện tại
                        - {{ number_format((float) $thongKe['tong_tien_thanh_cong'], 0, ',', '.') }} VNĐ đã thu thành công
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Giao dịch</th>
                            <th>Hóa đơn</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Phương thức</th>
                            <th>Nguồn</th>
                            <th>Thời điểm</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Xử lý bởi</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($danhSachThanhToan as $thanhToan)
                            @php
                                $chip = $mapTrangThai[$thanhToan->trang_thai] ?? 'chip chip-neutral';
                            @endphp
                            <tr>
                                <td style="min-width: 200px;">
                                    <div class="payment-code">{{ $thanhToan->ma_thanh_toan }}</div>
                                    <div class="table-subtext">{{ $thanhToan->ghi_chu ?: 'Không có ghi chú bổ sung' }}</div>
                                </td>
                                <td style="min-width: 180px;">
                                    <div class="fw-semibold">{{ $thanhToan->hoaDon?->ma_hoa_don ?? '-' }}</div>
                                    <div class="table-subtext">{{ $thanhToan->hoaDon?->datPhong?->ma_dat_phong ?? '-' }}</div>
                                </td>
                                <td style="min-width: 180px;">
                                    <div class="fw-semibold">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->ho_ten ?? '-' }}</div>
                                    <div class="table-subtext">{{ $thanhToan->hoaDon?->datPhong?->khachHang?->so_dien_thoai ?? '-' }}</div>
                                </td>
                                <td class="fw-bold">{{ number_format((float) $thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                <td>{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->phuong_thuc_thanh_toan) }}</td>
                                <td style="min-width: 180px;">
                                    <div class="fw-semibold">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->nguon_tao) }}</div>
                                    <div class="table-subtext">{{ $thanhToan->ma_tham_chieu ?: 'Không có tham chiếu' }}</div>
                                </td>
                                <td>{{ optional($thanhToan->thoi_diem_thanh_toan)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td><span class="{{ $chip }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($thanhToan->trang_thai) }}</span></td>
                                <td>{{ $thanhToan->nguoiTao?->ho_ten ?? '-' }}</td>
                                <td style="min-width: 180px;">
                                    @if($thanhToan->nguoiXuLy)
                                        <div class="fw-semibold">{{ $thanhToan->nguoiXuLy->ho_ten }}</div>
                                        <div class="table-subtext">{{ optional($thanhToan->thoi_diem_xu_ly)->format('d/m/Y H:i') ?? '-' }}</div>
                                    @else
                                        <span class="table-subtext">Chưa xử lý</span>
                                    @endif
                                </td>
                                <td style="min-width: 200px;">
                                    @if($thanhToan->trang_thai === 'cho_xu_ly')
                                        <div class="d-flex gap-2 flex-wrap">
                                            <form method="POST" action="{{ route('thanh-toan.cap-nhat-trang-thai', $thanhToan) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="trang_thai" value="thanh_cong">
                                                <button class="btn btn-sm btn-outline-success">Duyệt</button>
                                            </form>
                                            <form method="POST" action="{{ route('thanh-toan.cap-nhat-trang-thai', $thanhToan) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="trang_thai" value="that_bai">
                                                <button class="btn btn-sm btn-outline-danger">Từ chối</button>
                                            </form>
                                            @if($thanhToan->hoaDon)
                                                <a href="{{ route('hoa-don.show', $thanhToan->hoaDon) }}" class="btn btn-sm btn-outline-secondary">Mở hóa đơn</a>
                                            @endif
                                        </div>
                                    @else
                                        <span class="table-subtext">Đã chốt</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-4">
                                    <div class="empty-state">Chưa có giao dịch nào phù hợp với bộ lọc hiện tại.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $danhSachThanhToan->links() }}</div>
        </div>
    </div>
@endsection
