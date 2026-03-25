<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraTaiKhoanHoatDong
{
    public function handle(Request $request, Closure $next): Response
    {
        $nguoiDung = auth()->user();

        if ($nguoiDung && $nguoiDung->trang_thai !== 'hoat_dong') {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị tạm khóa.');
        }

        return $next($request);
    }
}