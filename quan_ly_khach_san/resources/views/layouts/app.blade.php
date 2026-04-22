<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Khách sạn')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --bg: #f4f7fb;
            --ink: #112740;
            --line: #d5e1ee;
            --brand: #0f766e;
            --content-max: 1480px;
        }

        * {
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at 0% 0%, #dfefff 0, transparent 28%),
                var(--bg);
            color: var(--ink);
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 10px 24px rgba(16, 42, 67, 0.06);
        }

        .app-container {
            width: min(var(--content-max), calc(100vw - 32px));
            margin: 0 auto;
        }

        .brand {
            font-weight: 800;
            color: var(--ink);
        }

        .brand i {
            color: var(--brand);
        }

        .btn-brand {
            border: none;
            border-radius: 10px;
            padding: 8px 14px;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .btn-brand:hover {
            color: #fff;
            opacity: 0.96;
        }

        .top-links {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .top-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            font-weight: 600;
            text-decoration: none;
        }

        .top-link:hover {
            background: #f8fbff;
            color: var(--ink);
        }

        .page-wrap {
            padding: 30px 0 44px;
        }

        .page-content {
            display: grid;
            gap: 24px;
        }

        .card-soft {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(16, 42, 67, 0.08);
        }

        .btn-outline-secondary,
        .btn-brand {
            min-height: 42px;
        }

        .form-control,
        .form-select {
            min-height: 48px;
            padding: 0.75rem 0.95rem;
        }

        textarea.form-control {
            min-height: 120px;
        }

        .table-responsive {
            width: 100%;
            overflow: auto;
            border: 1px solid #e2eaf3;
            border-radius: 18px;
            background: #fff;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #f6f9fc;
            color: #2b465f;
            border-bottom: 1px solid var(--line);
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
            padding: 14px 16px;
        }

        .table tbody td {
            border-bottom: 1px solid #edf2f8;
            padding: 14px 16px;
            vertical-align: middle;
        }

        .alert {
            margin-bottom: 0;
            border-radius: 14px;
        }

        @media (max-width: 991px) {
            .app-container {
                width: calc(100vw - 24px);
            }

            .topbar .app-container {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .top-links {
                width: 100%;
            }

            .page-wrap {
                padding: 22px 0 34px;
            }

            .page-content {
                gap: 18px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <header class="topbar">
        <div class="app-container py-3 d-flex align-items-center justify-content-between gap-2">
            <a href="{{ route('booking.index') }}" class="brand text-decoration-none">
                <i class="fa-solid fa-hotel me-2"></i>Quản lý khách sạn - Nhóm 6
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(auth()->user()->vai_tro === 'khach_hang')
                        <div class="top-links">
                            <a href="{{ route('booking.index') }}" class="top-link">Đặt phòng</a>
                            <a href="{{ route('booking.account') }}" class="top-link">Tài khoản</a>
                            <a href="{{ route('booking.account') }}#payment-section" class="top-link">Thanh toán</a>
                        </div>
                    @endif
                    <span class="text-muted small">Xin chào, {{ auth()->user()->ho_ten }}</span>
                    <a href="{{ route('logout') }}" class="btn btn-brand btn-sm">Đăng xuất</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-3">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-brand btn-sm">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-wrap">
        <div class="app-container page-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
