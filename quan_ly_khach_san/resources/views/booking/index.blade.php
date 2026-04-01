<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt phòng trực tuyến | Azure Bay Hotel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg: #f3f7fb;
            --ink-900: #10243e;
            --ink-700: #3f5673;
            --ink-500: #6f86a1;
            --line: #d7e3ef;
            --brand: #0f766e;
            --brand-dark: #0a5d57;
            --accent: #d97706;
        }

        * {
            font-family: 'Be Vietnam Pro', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 5% 0%, #dfefff 0, transparent 30%),
                radial-gradient(circle at 95% 95%, #daf6ee 0, transparent 30%),
                var(--bg);
        }

        .shell {
            max-width: 1220px;
            margin: 0 auto;
            padding: 20px 16px 40px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 16px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--ink-900);
            text-decoration: none;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #0d9488);
        }

        .top-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-soft,
        .btn-danger-soft {
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            color: var(--ink-900);
            font-weight: 600;
            padding: 9px 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-soft:hover {
            border-color: #bfcfe0;
            background: #f8fbff;
        }

        .btn-danger-soft {
            border: 0;
            color: #fff;
            background: #dc2626;
        }

        .btn-danger-soft:hover {
            background: #c81f1f;
        }

        .hero {
            border-radius: 26px;
            overflow: hidden;
            background:
                linear-gradient(130deg, rgba(10, 35, 59, 0.95), rgba(13, 71, 121, 0.92)),
                url('https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;
            box-shadow: 0 24px 48px rgba(9, 28, 50, 0.22);
            color: #fff;
            margin-bottom: 20px;
        }

        .hero-inner {
            padding: 30px;
            background: linear-gradient(130deg, rgba(10, 35, 59, 0.85), rgba(13, 71, 121, 0.7));
        }

        .hero h1 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 2.75rem);
            line-height: 1.28;
        }

        .hero p {
            margin: 10px 0 0;
            color: #dbe9f7;
            max-width: 780px;
        }

        .filter-box {
            margin-top: 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            padding: 14px;
        }

        .form-label-light {
            color: #dff0ff;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border: 1px solid var(--line);
            min-height: 44px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6ab8b0;
            box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.16);
        }

        .btn-brand {
            border: none;
            border-radius: 12px;
            padding: 11px 16px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .btn-brand:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-dark), #0f766e);
        }

        .content-card {
            border-radius: 22px;
            border: 1px solid var(--line);
            background: #fff;
            box-shadow: 0 18px 38px rgba(16, 42, 67, 0.08);
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin: 0;
        }

        .section-subtitle {
            color: var(--ink-500);
            margin: 4px 0 0;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .room-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.2s ease;
            height: 100%;
        }

        .room-card.active {
            border-color: #34a39a;
            box-shadow: 0 0 0 2px rgba(52, 163, 154, 0.2);
            transform: translateY(-2px);
        }

        .room-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .tag {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #155e75;
            background: #e0f2fe;
        }

        .price {
            text-align: right;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--brand);
            white-space: nowrap;
        }

        .price small {
            display: block;
            color: var(--ink-500);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .room-meta {
            color: var(--ink-700);
            display: grid;
            gap: 5px;
            font-size: 0.9rem;
        }

        .room-meta i {
            width: 16px;
            color: #0f766e;
        }

        .btn-choose {
            border: 1px solid #bcd2e5;
            border-radius: 12px;
            padding: 10px;
            font-weight: 700;
            color: var(--ink-900);
            background: #f8fbff;
            transition: all 0.2s ease;
        }

        .btn-choose:hover {
            border-color: #8fb4d5;
            background: #edf5ff;
        }

        .selected-room {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            background: #ebf5ff;
            color: #1d4ed8;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .chip-warning { background: #fef3c7; color: #92400e; }
        .chip-info { background: #dbeafe; color: #1d4ed8; }
        .chip-primary { background: #e0e7ff; color: #3730a3; }
        .chip-success { background: #dcfce7; color: #166534; }
        .chip-danger { background: #ffe4e6; color: #be123c; }
        .chip-neutral { background: #e2e8f0; color: #334155; }

        .alert {
            border-radius: 12px;
        }

        .muted-empty {
            border: 1px dashed #b8cbe0;
            border-radius: 14px;
            padding: 16px;
            color: var(--ink-500);
            background: #fbfdff;
        }

        .table thead th {
            background: #f6f9fc;
            color: #2b465f;
            border-bottom: 1px solid var(--line);
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .table tbody td {
            border-bottom: 1px solid #eef3f8;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #fbfdff;
        }

        .footer-note {
            text-align: center;
            color: var(--ink-500);
            margin-top: 18px;
            font-size: 0.86rem;
        }

        @media (max-width: 991px) {
            .room-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .room-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }

            .hero-inner {
                padding: 22px 18px;
            }

            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <div class="topbar">
            <a href="{{ route('booking.index') }}" class="brand">
                <span class="brand-icon"><i class="fa-solid fa-hotel"></i></span>
                <span>
                    Azure Bay Hotel
                    <small class="d-block text-muted fw-semibold">Đặt phòng trực tuyến 24/7</small>
                </span>
            </a>

            <div class="top-actions">
                @auth
                    @if(auth()->user()->vai_tro === 'khach_hang')
                        <span class="fw-semibold text-muted">Xin chào, {{ auth()->user()->ho_ten }}</span>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-soft">
                            <i class="fa-solid fa-chart-line"></i>
                            Vào trang quản trị
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn-danger-soft">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-soft">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn-soft">
                        <i class="fa-solid fa-user-plus"></i>
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>

        <section class="hero">
            <div class="hero-inner">
                <h1>Chọn phòng đẹp, đặt phòng nhanh, theo dõi đơn ngay tại trang chủ.</h1>
                <p>
                    Hệ thống giúp bạn tìm phòng trống theo ngày nhận/trả, gửi yêu cầu đặt phòng và cập nhật tiến độ xử lý theo thời gian thực.
                </p>

                <form method="GET" action="{{ route('booking.index') }}" class="filter-box">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label for="ngay_nhan" class="form-label-light">Ngày nhận phòng</label>
                            <input
                                type="date"
                                id="ngay_nhan"
                                name="ngay_nhan"
                                class="form-control"
                                value="{{ old('ngay_nhan', $boLoc['ngay_nhan']) }}"
                            >
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="ngay_tra" class="form-label-light">Ngày trả phòng</label>
                            <input
                                type="date"
                                id="ngay_tra"
                                name="ngay_tra"
                                class="form-control"
                                value="{{ old('ngay_tra', $boLoc['ngay_tra']) }}"
                            >
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="so_khach" class="form-label-light">Số khách</label>
                            <input
                                type="number"
                                min="1"
                                max="10"
                                id="so_khach"
                                name="so_khach"
                                class="form-control"
                                value="{{ old('so_khach', $boLoc['so_khach']) }}"
                                placeholder="Ví dụ: 2"
                            >
                        </div>
                        <div class="col-lg-3 col-md-6 d-grid">
                            <button type="submit" class="btn-brand">
                                <i class="fa-solid fa-magnifying-glass me-1"></i>Tìm phòng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Vui lòng kiểm tra lại thông tin:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="content-card p-4 mb-4">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Danh sách phòng khả dụng</h2>
                    <p class="section-subtitle">Hiện có {{ $danhSachPhong->total() }} phòng phù hợp với bộ lọc.</p>
                </div>
            </div>

            @if($danhSachPhong->isEmpty())
                <div class="muted-empty">
                    Không tìm thấy phòng phù hợp trong khoảng thời gian đã chọn. Bạn có thể đổi ngày hoặc giảm số lượng khách để xem thêm lựa chọn.
                </div>
            @else
                <div class="room-grid">
                    @foreach($danhSachPhong as $phong)
                        @php
                            $giaPhong = (float) ($phong->gia_mac_dinh ?? $phong->loaiPhong->gia_mot_dem ?? 0);
                            $dangDuocChon = (int) old('phong_id') === $phong->id;
                            $tenLoaiPhong = $phong->loaiPhong->ten_loai_phong ?? 'Loại phòng chưa cập nhật';
                        @endphp

                        <article
                            class="room-card {{ $dangDuocChon ? 'active' : '' }}"
                            data-room-id="{{ $phong->id }}"
                            data-room-name="Phòng {{ $phong->so_phong }} - {{ $tenLoaiPhong }}"
                        >
                            <div class="room-top">
                                <div>
                                    <span class="tag">Phòng {{ $phong->so_phong }}</span>
                                    <h3 class="h5 mt-2 mb-0">{{ $tenLoaiPhong }}</h3>
                                </div>
                                <div class="price">
                                    {{ number_format($giaPhong, 0, ',', '.') }}
                                    <small>VNĐ / đêm</small>
                                </div>
                            </div>

                            <div class="room-meta">
                                <div><i class="fa-solid fa-users"></i>Tối đa {{ $phong->loaiPhong->so_nguoi_toi_da ?? 1 }} khách</div>
                                <div><i class="fa-solid fa-bed"></i>{{ $phong->loaiPhong->so_giuong ?? 1 }} giường - {{ $phong->loaiPhong->loai_giuong ?? 'Tiêu chuẩn' }}</div>
                                @if($phong->tang)
                                    <div><i class="fa-solid fa-building"></i>Tầng {{ $phong->tang }}</div>
                                @endif
                                @if(!empty($phong->loaiPhong->mo_ta))
                                    <div><i class="fa-solid fa-circle-info"></i>{{ $phong->loaiPhong->mo_ta }}</div>
                                @endif
                            </div>

                            <button type="button" class="btn-choose js-choose-room" data-room-id="{{ $phong->id }}">
                                <i class="fa-solid fa-check me-1"></i>Chọn phòng này
                            </button>
                        </article>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $danhSachPhong->links() }}
                </div>
            @endif
        </section>

        <section id="booking-form" class="content-card p-4 mb-4">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Gửi yêu cầu đặt phòng</h2>
                    <p class="section-subtitle">Nhân viên sẽ liên hệ xác nhận ngay sau khi nhận yêu cầu.</p>
                </div>
            </div>

            @guest
                <div class="alert alert-warning mb-0">
                    Bạn vui lòng đăng nhập để đặt phòng trực tuyến.
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <a href="{{ route('login') }}" class="btn-soft">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn-soft">Đăng ký</a>
                    </div>
                </div>
            @else
                @if(auth()->user()->vai_tro !== 'khach_hang')
                    <div class="alert alert-warning mb-0">
                        Tài khoản hiện tại là tài khoản nội bộ. Vui lòng dùng tài khoản khách hàng để đặt phòng online.
                    </div>
                @else
                    <div class="selected-room mb-3" id="selected-room-label">
                        <i class="fa-solid fa-door-open"></i>
                        <span>
                            {{ old('phong_id') ? 'Phòng đã chọn: ID ' . old('phong_id') : 'Bạn chưa chọn phòng. Hãy chọn phòng ở danh sách phía trên.' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('booking.store') }}" class="row g-3">
                        @csrf
                        <input type="hidden" id="phong_id_input" name="phong_id" value="{{ old('phong_id') }}">

                        <div class="col-lg-4">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" name="ho_ten" value="{{ old('ho_ten', auth()->user()->ho_ten) }}" required>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="so_dien_thoai" value="{{ old('so_dien_thoai', auth()->user()->so_dien_thoai) }}">
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Ngày nhận phòng</label>
                            <input type="date" class="form-control" name="ngay_nhan" value="{{ old('ngay_nhan', $boLoc['ngay_nhan']) }}" required>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Ngày trả phòng</label>
                            <input type="date" class="form-control" name="ngay_tra" value="{{ old('ngay_tra', $boLoc['ngay_tra']) }}" required>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Số người lớn</label>
                            <input type="number" min="1" max="10" class="form-control" name="so_nguoi_lon" value="{{ old('so_nguoi_lon', 1) }}" required>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Số trẻ em</label>
                            <input type="number" min="0" max="10" class="form-control" name="so_tre_em" value="{{ old('so_tre_em', 0) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Yêu cầu đặc biệt</label>
                            <textarea class="form-control" rows="3" name="yeu_cau_dac_biet" placeholder="Ví dụ: phòng tầng cao, nhận phòng muộn, cần nôi em bé...">{{ old('yeu_cau_dac_biet') }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn-brand">
                                <i class="fa-solid fa-paper-plane me-1"></i>Gửi yêu cầu đặt phòng
                            </button>
                        </div>
                    </form>
                @endif
            @endguest
        </section>

        @auth
            @if(auth()->user()->vai_tro === 'khach_hang')
                <section class="content-card p-4">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Đơn đặt phòng của tôi</h2>
                            <p class="section-subtitle">Theo dõi trạng thái đơn đặt và thanh toán tại một nơi.</p>
                        </div>
                    </div>

                    @if($danhSachDonCuaToi->isEmpty())
                        <div class="muted-empty">Bạn chưa có đơn đặt phòng nào.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Phòng</th>
                                        <th>Lịch ở</th>
                                        <th>Trạng thái đơn</th>
                                        <th>Hóa đơn</th>
                                        <th>Đã thu</th>
                                        <th>Còn lại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($danhSachDonCuaToi as $don)
                                        @php
                                            $phong = $don->chiTietDatPhong->first()?->phong;
                                            $hoaDon = $don->hoaDon->where('trang_thai', '!=', 'da_huy')->first();
                                            $tongTien = (float) ($hoaDon->tong_tien ?? 0);
                                            $soTienDaThu = $hoaDon ? (float) $hoaDon->thanhToan->where('trang_thai', 'thanh_cong')->sum('so_tien') : 0;
                                            $soTienConLai = max(0, $tongTien - $soTienDaThu);

                                            $mapTrangThaiDon = [
                                                'cho_xac_nhan' => 'chip-warning',
                                                'da_xac_nhan' => 'chip-info',
                                                'da_nhan_phong' => 'chip-primary',
                                                'da_tra_phong' => 'chip-success',
                                                'da_huy' => 'chip-danger',
                                            ];
                                            $chipDon = $mapTrangThaiDon[$don->trang_thai] ?? 'chip-neutral';
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $don->ma_dat_phong }}</td>
                                            <td>{{ $phong ? 'Phòng ' . $phong->so_phong : '-' }}</td>
                                            <td>
                                                {{ optional($don->ngay_nhan_phong_du_kien)->format('d/m/Y') }}
                                                -
                                                {{ optional($don->ngay_tra_phong_du_kien)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <span class="chip {{ $chipDon }}">{{ \App\Support\HienThiGiaTri::nhanGiaTri($don->trang_thai) }}</span>
                                            </td>
                                            <td>
                                                @if($hoaDon)
                                                    {{ $hoaDon->ma_hoa_don }}
                                                    <div class="small text-muted">{{ \App\Support\HienThiGiaTri::nhanGiaTri($hoaDon->trang_thai) }}</div>
                                                @else
                                                    Chưa lập
                                                @endif
                                            </td>
                                            <td class="fw-semibold text-success">{{ number_format($soTienDaThu, 0, ',', '.') }} VNĐ</td>
                                            <td class="fw-semibold {{ $soTienConLai > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($soTienConLai, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif
        @endauth

        <div class="footer-note">Azure Bay Hotel | Nền tảng đặt phòng trực tuyến</div>
    </main>

    <script>
        const roomCards = Array.from(document.querySelectorAll('[data-room-id]'));
        const roomButtons = Array.from(document.querySelectorAll('.js-choose-room'));
        const roomInput = document.getElementById('phong_id_input');
        const selectedRoomText = document.querySelector('#selected-room-label span');

        function setSelectedRoom(roomId) {
            const roomCard = roomCards.find((item) => String(item.dataset.roomId) === String(roomId));
            roomCards.forEach((item) => item.classList.remove('active'));

            if (!roomCard) {
                if (roomInput) {
                    roomInput.value = '';
                }

                if (selectedRoomText) {
                    selectedRoomText.textContent = 'Bạn chưa chọn phòng. Hãy chọn phòng ở danh sách phía trên.';
                }
                return;
            }

            roomCard.classList.add('active');

            if (roomInput) {
                roomInput.value = roomId;
            }

            if (selectedRoomText) {
                selectedRoomText.textContent = 'Phòng đã chọn: ' + roomCard.dataset.roomName;
            }
        }

        roomButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const roomId = button.dataset.roomId;
                setSelectedRoom(roomId);
                document.getElementById('booking-form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        if (roomInput && roomInput.value) {
            setSelectedRoom(roomInput.value);
        }
    </script>
</body>
</html>
