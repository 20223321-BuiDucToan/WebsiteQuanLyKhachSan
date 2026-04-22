<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách sạn - Nhóm 6</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Be Vietnam Pro', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.12), transparent 28%),
                linear-gradient(180deg, #f4f8fc, #eef4fa);
            color: #10243e;
        }

        .fallback-shell {
            width: min(1180px, calc(100vw - 32px));
            margin: 0 auto;
            padding: clamp(28px, 4vw, 56px) 0;
        }

        .fallback-card {
            border: 1px solid #d9e4ef;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 48px rgba(16, 42, 67, 0.08);
        }

        .fallback-card .card-body {
            padding: clamp(28px, 3vw, 40px);
        }

        .fallback-note {
            max-width: 72ch;
            line-height: 1.75;
            color: #5f748f;
        }
    </style>
</head>
<body>
    <div class="fallback-shell">
        <div class="card fallback-card border-0">
            <div class="card-body">
                <h1 class="h3 mb-3">Giao diện dự phòng</h1>
                <p class="fallback-note mb-0">
                    File này chỉ để tương thích kỹ thuật cũ. Giao diện chính đã chuyển sang hệ thống Blade mới trong thư mục
                    <code>resources/views/layouts</code>, với bố cục rộng hơn và tối ưu hơn cho cả khách hàng lẫn bộ phận nội bộ.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
