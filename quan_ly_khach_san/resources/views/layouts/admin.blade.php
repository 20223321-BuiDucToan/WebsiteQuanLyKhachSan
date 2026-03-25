<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản trị khách sạn')</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .sidebar {
            width: 270px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
            color: #fff;
            padding: 24px 18px;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
        }

        .brand-box {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.08);
            margin-bottom: 20px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
            color: #fff;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #cbd5e1;
            margin-top: 4px;
        }

        .role-box {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.06);
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .role-label {
            font-size: 11px;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }

        .role-value {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            text-transform: capitalize;
        }

        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 8px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #fff;
            transform: translateX(4px);
        }

        .menu-title {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 18px 0 10px;
            padding-left: 4px;
        }

        .main-content {
            margin-left: 270px;
            min-height: 100vh;
        }

        .topbar {
            background: #ffffff;
            padding: 18px 28px;
            box-shadow: 0 2px 18px rgba(15, 23, 42, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-wrapper {
            padding: 28px;
        }

        .premium-card {
            border: none;
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .stat-card {
            border-radius: 22px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .gradient-1 { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .gradient-2 { background: linear-gradient(135deg, #7c3aed, #a855f7); }
        .gradient-3 { background: linear-gradient(135deg, #059669, #10b981); }
        .gradient-4 { background: linear-gradient(135deg, #ea580c, #f97316); }

        .stat-title {
            font-size: 14px;
            font-weight: 600;
            opacity: 0.9;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 800;
            margin-top: 8px;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            color: #334155;
            font-weight: 700;
        }

        .badge-role-admin {
            background: #fee2e2;
            color: #b91c1c;
            padding: 8px 12px;
            border-radius: 999px;
        }

        .badge-role-nhan-vien {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 8px 12px;
            border-radius: 999px;
        }

        .badge-status-hoat-dong {
            background: #dcfce7;
            color: #15803d;
            padding: 8px 12px;
            border-radius: 999px;
        }

        .badge-status-tam-khoa {
            background: #fef3c7;
            color: #b45309;
            padding: 8px 12px;
            border-radius: 999px;
        }

        .btn-premium {
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #fff;
        }

        .btn-gradient:hover {
            color: #fff;
            opacity: 0.95;
        }

        .avatar-mini {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
        }

        .section-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

        .section-subtitle {
            color: #64748b;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand-box">
            <h1 class="brand-title"><i class="fa-solid fa-hotel me-2"></i>Luxury Hotel</h1>
            <div class="brand-subtitle">Hệ thống quản lý khách sạn</div>
        </div>

        <div class="role-box">
            <div class="role-label">Vai trò hiện tại</div>
            <div class="role-value">
                {{ auth()->user()->vai_tro === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}
            </div>
        </div>

        <nav class="nav flex-column">
            <div class="menu-title">Tổng quan</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line me-2"></i> Trang chủ
            </a>

            @if(auth()->user()->vai_tro === 'admin')
                <div class="menu-title">Quản trị hệ thống</div>

                <a href="{{ route('nguoi-dung.index') }}" class="nav-link {{ request()->routeIs('nguoi-dung.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users me-2"></i> Quản lý người dùng
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-bed me-2"></i> Quản lý phòng
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-file-invoice-dollar me-2"></i> Quản lý hóa đơn
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-chart-pie me-2"></i> Báo cáo thống kê
                </a>
            @endif

            @if(auth()->user()->vai_tro === 'nhan_vien')
                <div class="menu-title">Nghiệp vụ hằng ngày</div>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-calendar-check me-2"></i> Đặt phòng
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-users-line me-2"></i> Khách hàng
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-credit-card me-2"></i> Thanh toán
                </a>

                <a href="#" class="nav-link">
                    <i class="fa-solid fa-receipt me-2"></i> Hóa đơn
                </a>
            @endif
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                <div class="fw-bold fs-5">Xin chào, {{ auth()->user()->ho_ten ?? 'Người dùng' }}</div>

                @if(auth()->user()->vai_tro === 'admin')
                    <small class="text-muted">Khu vực quản trị và giám sát toàn bộ hệ thống khách sạn</small>
                @else
                    <small class="text-muted">Khu vực thao tác nghiệp vụ dành cho nhân viên vận hành</small>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                @if(auth()->user()->vai_tro === 'admin')
                    <span class="badge-role-admin">Admin</span>
                @else
                    <span class="badge-role-nhan-vien">Nhân viên</span>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>

        <div class="page-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>