<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
</head>
<body style="margin:0;padding:0;background:#f3f7fb;font-family:Arial,sans-serif;color:#10243e;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f7fb;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #d7e3ef;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#0f766e;color:#ffffff;padding:22px 26px;">
                            <div style="font-size:20px;font-weight:700;">Quản lý khách sạn - Nhóm 4</div>
                            <div style="font-size:13px;margin-top:6px;color:#dff7f3;">Yêu cầu đặt lại mật khẩu</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:26px;">
                            <p style="margin:0 0 14px;">Xin chào {{ $nguoiDung->ho_ten ?? $nguoiDung->ten_dang_nhap }},</p>
                            <p style="margin:0 0 18px;line-height:1.6;">
                                Hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Liên kết bên dưới có hiệu lực trong 60 phút.
                            </p>

                            <p style="margin:24px 0;text-align:center;">
                                <a href="{{ $resetLink }}" style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:10px;">
                                    Đặt lại mật khẩu
                                </a>
                            </p>

                            <p style="margin:0 0 14px;line-height:1.6;color:#405a76;font-size:14px;">
                                Nếu nút không hoạt động, hãy mở liên kết sau:
                            </p>
                            <p style="margin:0;word-break:break-all;color:#0f766e;font-size:13px;">{{ $resetLink }}</p>

                            <p style="margin:22px 0 0;line-height:1.6;color:#405a76;font-size:14px;">
                                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
