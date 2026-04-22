<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách sạn - Nhóm 6</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Be Vietnam Pro', sans-serif;
            background: linear-gradient(130deg, #0d2f4f, #14507c);
            color: #fff;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .box {
            width: min(960px, 100%);
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 24px;
            padding: clamp(34px, 5vw, 54px);
            backdrop-filter: blur(6px);
            box-shadow: 0 28px 60px rgba(5, 18, 32, 0.28);
        }
        h1 {
            margin: 0 0 10px;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 4vw, 3rem);
        }
        p {
            max-width: 64ch;
            margin: 0 auto 26px;
            color: #d7e8f8;
            line-height: 1.7;
        }
        .actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
        }
        .btn-main {
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #0d9488);
        }
        .btn-sub {
            color: #0d2f4f;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Quản lý khách sạn - Nhóm 6</h1>
        <p>Đặt phòng và quản lý khách sạn cho cả khách hàng, nhân viên và quản trị viên.</p>

        <div class="actions">
            <a href="{{ route('booking.index') }}" class="btn btn-main">Vào trang đặt phòng</a>
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="btn btn-sub">Đăng nhập</a>
            @endif
        </div>
    </div>
</body>
</html>
