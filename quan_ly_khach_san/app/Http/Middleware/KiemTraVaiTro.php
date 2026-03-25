<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraVaiTro
{
    public function handle(Request $request, Closure $next, ...$vaiTro): Response
    {
        $nguoiDung = auth()->user();

        if (!$nguoiDung) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        if (!in_array($nguoiDung->vai_tro, $vaiTro)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}