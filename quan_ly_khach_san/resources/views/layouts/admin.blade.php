<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ thống quản lý khách sạn')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-page: #f4f6fb;
            --bg-card: #ffffff;
            --ink-900: #0e1f35;
            --ink-700: #324a66;
            --ink-500: #68839f;
            --line: #d9e4ef;
            --brand: #0f766e;
            --brand-dark: #0a5f58;
            --accent: #d97706;
            --danger: #be123c;
            --success: #166534;
            --sidebar-1: #0f2944;
            --sidebar-2: #1a3e61;
            --shadow-soft: 0 18px 42px rgba(15, 41, 68, 0.1);
        }

        * {
            font-family: 'Be Vietnam Pro', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 8% 0%, #dceeff 0, transparent 30%),
                radial-gradient(circle at 100% 100%, #d7f7ef 0, transparent 30%),
                var(--bg-page);
        }

        a {
            text-decoration: none;
        }

        .app-shell {
            min-height: 100vh;
        }

        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 290px;
            overflow-y: auto;
            background: linear-gradient(160deg, var(--sidebar-1), var(--sidebar-2));
            color: #dbe9f7;
            padding: 22px 16px 24px;
            box-shadow: 16px 0 34px rgba(7, 22, 39, 0.25);
            z-index: 1050;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 16px;
            margin-bottom: 16px;
        }

        .sidebar-brand h1 {
            margin: 0;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .sidebar-brand p {
            margin: 5px 0 0;
            font-size: 0.82rem;
            color: #d2e2f3;
        }

        .role-card {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        .role-card small {
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 0.69rem;
            color: #c8d9ec;
            margin-bottom: 3px;
        }

        .role-card strong {
            color: #fff;
            font-size: 1rem;
        }

        .menu-section {
            margin: 16px 8px 8px;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            font-size: 0.68rem;
            font-weight: 700;
            color: #a8c3df;
        }

        .menu-list {
            display: grid;
            gap: 6px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            color: #d9e8f7;
            font-size: 0.93rem;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .menu-link:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.09);
            transform: translateX(3px);
        }

        .menu-link.active {
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #0e9f93);
            border-color: rgba(255, 255, 255, 0.22);
            box-shadow: 0 12px 24px rgba(13, 148, 136, 0.28);
        }

        .sidebar-note {
            margin-top: 16px;
            color: #c0d5eb;
            font-size: 0.8rem;
            line-height: 1.6;
            border-top: 1px solid rgba(255, 255, 255, 0.13);
            padding-top: 12px;
        }

        .app-main {
            margin-left: 290px;
            min-height: 100vh;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .topbar h2 {
            margin: 0;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .topbar p {
            margin: 3px 0 0;
            color: var(--ink-500);
            font-size: 0.85rem;
        }

        .btn-menu {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink-700);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: none;
            place-items: center;
        }

        .page-wrap {
            padding: 24px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.75rem, 2vw, 2.25rem);
            margin: 0;
            color: var(--ink-900);
        }

        .section-subtitle {
            margin: 6px 0 0;
            color: var(--ink-500);
        }

        .premium-card {
            border: 1px solid var(--line);
            border-radius: 22px;
            background: var(--bg-card);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .metric-card {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
            padding: 16px;
            height: 100%;
        }

        .metric-label {
            color: var(--ink-500);
            font-size: 0.84rem;
            margin-bottom: 6px;
        }

        .metric-value {
            font-size: 1.85rem;
            line-height: 1.05;
            font-weight: 800;
            color: var(--ink-900);
        }

        .btn-gradient,
        .btn-premium {
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .btn-gradient:hover,
        .btn-premium:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-dark), #0f766e);
        }

        .btn-soft {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 9px 14px;
            background: #fff;
            color: var(--ink-700);
            font-weight: 600;
        }

        .btn-soft:hover {
            border-color: #bdcede;
            background: #f8fbff;
        }

        .form-label {
            font-weight: 600;
            color: #2f4964;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-color: #cddaea;
            border-radius: 12px;
            min-height: 44px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #63b5ab;
            box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.14);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f6f9fc;
            color: #2b465f;
            border-bottom: 1px solid var(--line);
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
            padding: 12px;
        }

        .table tbody td {
            border-bottom: 1px solid #edf2f8;
            padding: 12px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #fbfdff;
        }

        .chip,
        .badge-status-hoat-dong,
        .badge-status-tam-khoa,
        .badge-role-admin,
        .badge-role-nhan-vien,
        .badge-role-khach-hang {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
        }

        .chip-success,
        .badge-status-hoat-dong {
            color: #166534;
            background: #dcfce7;
        }

        .chip-danger,
        .badge-status-tam-khoa {
            color: #be123c;
            background: #ffe4e6;
        }

        .chip-warning {
            color: #92400e;
            background: #fef3c7;
        }

        .chip-info,
        .badge-role-nhan-vien {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .chip-neutral {
            color: #334155;
            background: #e2e8f0;
        }

        .badge-role-admin {
            color: #9f1239;
            background: #fce7f3;
        }

        .badge-role-khach-hang {
            color: #166534;
            background: #dcfce7;
        }

        .alert {
            border-radius: 14px;
            border: 1px solid transparent;
        }

        .pagination {
            margin-bottom: 0;
            gap: 6px;
        }

        .page-item .page-link {
            border: 1px solid #d2deeb;
            border-radius: 10px;
            color: #35516d;
            font-weight: 600;
            min-width: 38px;
            text-align: center;
        }

        .page-item.active .page-link {
            border-color: var(--brand);
            background: var(--brand);
            color: #fff;
        }

        .page-item.disabled .page-link {
            color: #94a3b8;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 22, 37, 0.5);
            z-index: 1040;
        }

        .mobile-overlay.show {
            display: block;
        }

        @media (max-width: 1199px) {
            .app-sidebar {
                width: 268px;
            }

            .app-main {
                margin-left: 268px;
            }
        }

        @media (max-width: 991px) {
            .btn-menu {
                display: inline-grid;
            }

            .app-sidebar {
                width: 280px;
                transform: translateX(-100%);
            }

            .app-shell.menu-open .app-sidebar {
                transform: translateX(0);
            }

            .app-main {
                margin-left: 0;
            }

            .topbar {
                padding: 12px 14px;
            }

            .page-wrap {
                padding: 16px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php
        $nguoiDungDangNhap = auth()->user();
        $vaiTro = $nguoiDungDangNhap->vai_tro;
        $tenVaiTro = match ($vaiTro) {
            'admin' => 'Quản trị viên',
            'nhan_vien' => 'Nhân viên',
            'khach_hang' => 'Khách hàng',
            default => ucfirst((string) $vaiTro),
        };
    @endphp

    <div class="app-shell" id="app-shell">
        <div class="mobile-overlay" id="mobile-overlay"></div>

        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <h1><i class="fa-solid fa-hotel me-2"></i>Azure Bay Hotel</h1>
                <p>Nền tảng quản lý khách sạn tập trung</p>
            </div>

            <div class="role-card">
                <small>Vai trò đăng nhập</small>
                <strong>{{ $tenVaiTro }}</strong>
            </div>

            <div class="menu-list">
                <div class="menu-section">Tổng quan</div>
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Bảng điều khiển</span>
                </a>

                @if($vaiTro === 'admin')
                    <div class="menu-section">Quản trị hệ thống</div>
                    <a href="{{ route('nguoi-dung.index') }}" class="menu-link {{ request()->routeIs('nguoi-dung.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Quản lý người dùng</span>
                    </a>
                    <a href="{{ route('phong.index') }}" class="menu-link {{ request()->routeIs('phong.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bed"></i>
                        <span>Quản lý phòng</span>
                    </a>
                    <a href="{{ route('bao-cao.index') }}" class="menu-link {{ request()->routeIs('bao-cao.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Báo cáo thống kê</span>
                    </a>
                @endif

                @if(in_array($vaiTro, ['admin', 'nhan_vien'], true))
                    <div class="menu-section">Nghiệp vụ vận hành</div>
                    <a href="{{ route('dat-phong.index') }}" class="menu-link {{ request()->routeIs('dat-phong.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Quản lý đặt phòng</span>
                    </a>
                    <a href="{{ route('khach-hang.index') }}" class="menu-link {{ request()->routeIs('khach-hang.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i>
                        <span>Quản lý khách hàng</span>
                    </a>
                    <a href="{{ route('hoa-don.index') }}" class="menu-link {{ request()->routeIs('hoa-don.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Quản lý hóa đơn</span>
                    </a>
                    <a href="{{ route('thanh-toan.index') }}" class="menu-link {{ request()->routeIs('thanh-toan.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Quản lý thanh toán</span>
                    </a>
                @endif
            </div>

            <div class="sidebar-note">
                Mọi thay đổi dữ liệu được ghi nhận để đảm bảo kiểm soát vận hành và truy vết.
            </div>
        </aside>

        <main class="app-main">
            <header class="topbar">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-menu" id="btn-open-menu" aria-label="Mở menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div>
                        <h2>Xin chào, {{ $nguoiDungDangNhap->ho_ten ?? 'Người dùng' }}</h2>
                        <p>Hôm nay {{ now()->format('d/m/Y') }}, chúc bạn làm việc hiệu quả.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    @if($vaiTro === 'admin')
                        <span class="badge-role-admin">ADMIN</span>
                    @elseif($vaiTro === 'nhan_vien')
                        <span class="badge-role-nhan-vien">NHÂN VIÊN</span>
                    @else
                        <span class="badge-role-khach-hang">KHÁCH HÀNG</span>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-soft btn-sm">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </header>

            <section class="page-wrap">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-2">Có lỗi dữ liệu cần kiểm tra:</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const appShell = document.getElementById('app-shell');
        const btnOpenMenu = document.getElementById('btn-open-menu');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function dongMenu() {
            appShell?.classList.remove('menu-open');
            mobileOverlay?.classList.remove('show');
        }

        function moMenu() {
            appShell?.classList.add('menu-open');
            mobileOverlay?.classList.add('show');
        }

        btnOpenMenu?.addEventListener('click', () => {
            if (appShell?.classList.contains('menu-open')) {
                dongMenu();
                return;
            }

            moMenu();
        });

        mobileOverlay?.addEventListener('click', dongMenu);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                dongMenu();
            }
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
