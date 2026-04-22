<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tài khoản khách sạn')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --ink-900: #0f1f33;
            --ink-600: #52657f;
            --line: #d8e3ef;
            --brand: #0f766e;
            --brand-dark: #0a5f58;
            --auth-shell-max: 1320px;
            --auth-form-max: 560px;
        }

        * {
            font-family: 'Be Vietnam Pro', sans-serif;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(125deg, rgba(8, 34, 59, 0.88), rgba(12, 58, 97, 0.82)),
                url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=2100&q=80') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(18px, 3vw, 36px);
        }

        .auth-shell {
            width: min(var(--auth-shell-max), 100%);
            min-height: 720px;
            border-radius: 28px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            box-shadow: 0 30px 70px rgba(6, 19, 35, 0.42);
        }

        .auth-aside {
            height: 100%;
            padding: clamp(36px, 3vw, 56px);
            background: linear-gradient(145deg, rgba(15, 118, 110, 0.92), rgba(8, 92, 81, 0.86));
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .auth-aside::before,
        .auth-aside::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
        }

        .auth-aside::before {
            width: 230px;
            height: 230px;
            top: -70px;
            right: -80px;
        }

        .auth-aside::after {
            width: 290px;
            height: 290px;
            bottom: -150px;
            left: -130px;
        }

        .aside-brand {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 52px;
            position: relative;
            z-index: 1;
        }

        .aside-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.8rem, 3vw, 2.45rem);
            line-height: 1.35;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .aside-text {
            color: #e8f6f2;
            line-height: 1.75;
            position: relative;
            z-index: 1;
        }

        .aside-points {
            margin-top: 28px;
            display: grid;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .aside-points span {
            font-size: 0.96rem;
            line-height: 1.7;
        }

        .auth-main {
            height: 100%;
            background: rgba(255, 255, 255, 0.96);
            padding: clamp(28px, 3vw, 48px);
        }

        .auth-main-inner {
            width: min(var(--auth-form-max), 100%);
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        .auth-top {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 6px;
        }

        .btn-home {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--ink-900);
            padding: 8px 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-home:hover {
            background: #f7fbff;
            border-color: #c3d4e7;
        }

        .auth-card-title {
            margin: 0 0 6px;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--ink-900);
        }

        .auth-card-subtitle {
            margin-bottom: 22px;
            color: var(--ink-600);
        }

        .form-label {
            font-weight: 600;
            color: #26415c;
            margin-bottom: 6px;
        }

        .form-control {
            min-height: 50px;
            border-radius: 12px;
            border-color: #ccd9e8;
            padding: 0.78rem 0.95rem;
        }

        .form-control:focus {
            border-color: #67b4ab;
            box-shadow: 0 0 0 0.2rem rgba(15, 118, 110, 0.15);
        }

        .btn-auth {
            border: none;
            border-radius: 12px;
            min-height: 48px;
            padding: 11px 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #0d9488);
        }

        .btn-auth:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--brand-dark), #0f766e);
        }

        .auth-link {
            color: #0f766e;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 0;
        }

        @media (max-width: 991px) {
            .auth-shell {
                min-height: auto;
            }

            .auth-main {
                padding: 24px 18px;
            }

            .auth-main-inner {
                gap: 16px;
            }

            .auth-top {
                margin-bottom: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xxl-10 col-xl-11">
                <div class="auth-shell">
                    <div class="row g-0 h-100">
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="auth-aside">
                                <div class="aside-brand">
                                    <i class="fa-solid fa-hotel me-2"></i>Quản lý khách sạn - Nhóm 6
                                </div>
                                <div class="aside-title"></div>
                                <div class="aside-text">
                                    Hệ thống hỗ trợ đầy đủ quy trình đặt phòng, chăm sóc khách hàng, thanh toán và theo dõi hóa đơn trên một giao diện trực quan.
                                </div>
                                <div class="aside-points">
                                    <span><i class="fa-solid fa-circle-check me-2"></i>Đặt phòng nhanh và theo dõi trạng thái tức thì</span>
                                    <span><i class="fa-solid fa-circle-check me-2"></i>Quản lý thông tin tài khoản an toàn</span>
                                    <span><i class="fa-solid fa-circle-check me-2"></i>Tối ưu vận hành cho admin và nhân viên</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="auth-main">
                                <div class="auth-main-inner">
                                    <div class="auth-top">
                                        <a href="{{ route('booking.index') }}" class="btn-home">
                                            <i class="fa-solid fa-house me-1"></i>Về trang chủ
                                        </a>
                                    </div>

                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
