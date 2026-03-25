<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hotel Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f4f9;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #1e3c72, #2a5298);
            color: white;
            position: fixed;
            width: 240px;
        }

        .sidebar h4 {
            text-align: center;
            padding: 20px 0;
            font-weight: 600;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 25px;
        }

        /* Content */
        .content {
            margin-left: 240px;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* Card */
        .card-custom {
            border-radius: 15px;
            color: white;
            padding: 20px;
            transition: 0.3s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }

        .bg-1 { background: linear-gradient(45deg, #667eea, #764ba2); }
        .bg-2 { background: linear-gradient(45deg, #43cea2, #185a9d); }
        .bg-3 { background: linear-gradient(45deg, #f7971e, #ffd200); }
        .bg-4 { background: linear-gradient(45deg, #ff416c, #ff4b2b); }

    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>🏨 Hotel</h4>

    <a href="{{ route('home') }}"><i class="fa fa-home"></i> Dashboard</a>

    @if(auth()->user()->vai_tro === 'admin')
        <a href="#"><i class="fa fa-users"></i> Người dùng</a>
    @endif

    <a href="#"><i class="fa fa-bed"></i> Phòng</a>
    <a href="#"><i class="fa fa-calendar"></i> Đặt phòng</a>
    <a href="#"><i class="fa fa-file-invoice"></i> Hóa đơn</a>
</div>

<!-- Content -->
<div class="content">

    <!-- Navbar -->
    <nav class="navbar px-4 py-2 d-flex justify-content-between">
        <span>Xin chào, <b>{{ auth()->user()->ho_ten }}</b></span>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger btn-sm">
                <i class="fa fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form>
    </nav>

    <!-- Main -->
    <div class="container-fluid p-4">
        @yield('content')
    </div>

</div>

</body>
</html>