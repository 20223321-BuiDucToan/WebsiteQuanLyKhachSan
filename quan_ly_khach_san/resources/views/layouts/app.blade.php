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

        .page-wrap {
            padding: 26px 0 36px;
        }

        .card-soft {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 14px 30px rgba(16, 42, 67, 0.08);
        }
    </style>

    @stack('styles')
</head>
<body>
    <header class="topbar">
        <div class="container py-3 d-flex align-items-center justify-content-between gap-2">
            <a href="{{ route('booking.index') }}" class="brand text-decoration-none">
                <i class="fa-solid fa-hotel me-2"></i>Azure Bay Hotel
            </a>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <span class="text-muted small">Xin chào, {{ auth()->user()->ho_ten }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="btn btn-brand btn-sm">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-3">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-brand btn-sm">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-wrap">
        <div class="container">
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
