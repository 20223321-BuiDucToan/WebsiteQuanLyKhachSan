<?php

namespace App\Providers;

use App\Models\Phong;
use App\Models\ThanhToan;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        $this->cauHinhDuongDanBienDichView();
        $this->dongBoTrangThaiPhongTheoNgay();
        $this->chiaSeCanhBaoThanhToanNoiBo();
    }

    private function cauHinhDuongDanBienDichView(): void
    {
        $duongDanHienTai = config('view.compiled');

        if ($this->damBaoThuMucCoTheGhi($duongDanHienTai)) {
            return;
        }

        $danhSachDuongDanDuPhong = [
            rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).'\\quan_ly_khach_san_views',
            sys_get_temp_dir(),
        ];

        foreach ($danhSachDuongDanDuPhong as $duongDanDuPhong) {
            if (! $this->damBaoThuMucCoTheGhi($duongDanDuPhong)) {
                continue;
            }

            config(['view.compiled' => $duongDanDuPhong]);

            if ($this->app->resolved('blade.compiler')) {
                $compiler = $this->app['blade.compiler'];

                (function () use ($duongDanDuPhong) {
                    $this->cachePath = $duongDanDuPhong;
                })->bindTo($compiler, $compiler)();
            }

            return;
        }
    }

    private function damBaoThuMucCoTheGhi(?string $duongDan): bool
    {
        if (! is_string($duongDan) || trim($duongDan) === '') {
            return false;
        }

        try {
            File::ensureDirectoryExists($duongDan);
        } catch (\Throwable) {
            return false;
        }

        return is_dir($duongDan) && is_writable($duongDan);
    }

    private function dongBoTrangThaiPhongTheoNgay(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            if (
                ! Schema::hasTable('phong')
                || ! Schema::hasTable('dat_phong')
                || ! Schema::hasTable('chi_tiet_dat_phong')
            ) {
                return;
            }

            $cacheKey = 'dong-bo-trang-thai-phong-' . now()->toDateString();

            if (Cache::get($cacheKey)) {
                return;
            }

            Cache::put($cacheKey, true, now()->endOfDay());

            Phong::query()
                ->get()
                ->each(fn (Phong $phong) => $phong->dongBoTrangThaiHeThong());
        } catch (\Throwable) {
            // Bo qua de khong chan request neu cache/database chua san sang.
        }
    }

    private function chiaSeCanhBaoThanhToanNoiBo(): void
    {
        View::composer('layouts.admin', function ($view) {
            $duLieuMacDinh = [
                'tong_cho_xu_ly' => 0,
                'cho_xu_ly_khach_hang' => 0,
            ];

            try {
                $nguoiDung = auth()->user();

                if (
                    ! $nguoiDung
                    || ! in_array($nguoiDung->vai_tro, ['admin', 'nhan_vien'], true)
                    || ! Schema::hasTable('thanh_toan')
                ) {
                    $view->with('canhBaoThanhToanNoiBo', $duLieuMacDinh);

                    return;
                }

                $tongChoXuLy = ThanhToan::query()
                    ->where('trang_thai', 'cho_xu_ly')
                    ->count();

                $tongChoXuLyTuKhach = ThanhToan::query()
                    ->where('trang_thai', 'cho_xu_ly')
                    ->where('nguon_tao', ThanhToan::NGUON_TAO_KHACH_HANG)
                    ->count();

                $view->with('canhBaoThanhToanNoiBo', [
                    'tong_cho_xu_ly' => $tongChoXuLy,
                    'cho_xu_ly_khach_hang' => $tongChoXuLyTuKhach,
                ]);
            } catch (\Throwable) {
                $view->with('canhBaoThanhToanNoiBo', $duLieuMacDinh);
            }
        });
    }
}
