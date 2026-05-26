<?php

use App\Services\XuLyDatPhongKhachHangQuaHanService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('booking:process-overdue-arrivals', function (XuLyDatPhongKhachHangQuaHanService $service) {
    $thongKe = $service->xuLy();

    $this->info('Da xu ly ' . $thongKe['tong_duoc_xu_ly'] . ' don khach online qua han.');
    $this->line('- Tu dong huy: ' . $thongKe['da_huy']);
    $this->line('- Chuyen khong den: ' . $thongKe['khong_den']);
})->purpose('Tu dong xu ly don khach online qua han nhan phong');

Schedule::command('booking:process-overdue-arrivals')
    ->everyFiveMinutes()
    ->withoutOverlapping();
