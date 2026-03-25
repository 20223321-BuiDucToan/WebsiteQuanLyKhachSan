<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Luxury Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(15,23,42,0.88), rgba(30,41,59,0.82)),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }

        .auth-left {
            background: linear-gradient(160deg, rgba(37,99,235,0.85), rgba(124,58,237,0.75));
            color: white;
            padding: 60px 50px;
            height: 100%;
        }

        .auth-left .brand {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 50px;
            letter-spacing: 1px;
        }

        .auth-left h2 {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .auth-left p {
            font-size: 15px;
            opacity: 0.92;
            line-height: 1.8;
        }

        .auth-feature {
            margin-top: 30px;
        }

        .auth-feature div {
            margin-bottom: 16px;
            font-size: 15px;
        }

        .auth-right {
            background: rgba(255,255,255,0.96);
            padding: 50px 40px;
            height: 100%;
        }

        .auth-card-title {
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .auth-card-subtitle {
            color: #64748b;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #dbe3ee;
            padding-left: 14px;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79,70,229,0.12);
        }

        .btn-auth {
            height: 48px;
            border-radius: 12px;
            font-weight: 600;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            border: none;
            color: white;
        }

        .btn-auth:hover {
            opacity: 0.95;
            color: white;
        }

        .auth-link {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
        }

        @media (max-width: 991px) {
            .auth-left {
                display: none;
            }

            .auth-wrapper {
                max-width: 520px;
            }

            .auth-right {
                padding: 35px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="auth-wrapper">
                    <div class="row g-0 h-100">
                        <div class="col-lg-6">
                            <div class="auth-left">
                                <div class="brand">
                                    <i class="fa-solid fa-hotel me-2"></i> HOTEL LUXURY
                                </div>

                                <h2>Hệ thống quản lý khách sạn hiện đại và chuyên nghiệp</h2>
                                <p>
                                    Quản lý đặt phòng, khách hàng, doanh thu, dịch vụ và vận hành khách sạn
                                    trên một nền tảng trực quan, mạnh mẽ và đẳng cấp.
                                </p>

                                <div class="auth-feature">
                                    <div><i class="fa-solid fa-circle-check me-2"></i> Quản lý người dùng và phân quyền</div>
                                    <div><i class="fa-solid fa-circle-check me-2"></i> Theo dõi phòng, đặt phòng, hóa đơn</div>
                                    <div><i class="fa-solid fa-circle-check me-2"></i> Dashboard chuyên nghiệp, dễ mở rộng</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="auth-right">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
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
</body>
</html>