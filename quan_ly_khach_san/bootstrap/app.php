<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {


        $middleware->alias([
        'kiem_tra_tai_khoan_hoat_dong' => \App\Http\Middleware\KiemTraTaiKhoanHoatDong::class,
        'kiem_tra_vai_tro' => \App\Http\Middleware\KiemTraVaiTro::class,
    ]);
    })  
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
