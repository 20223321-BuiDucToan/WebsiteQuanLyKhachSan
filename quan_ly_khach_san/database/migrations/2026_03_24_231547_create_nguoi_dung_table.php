<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            if (!Schema::hasColumn('nguoi_dung', 'so_dien_thoai')) {
                $table->string('so_dien_thoai', 20)->nullable()->after('password');
            }

            if (!Schema::hasColumn('nguoi_dung', 'dia_chi')) {
                $table->string('dia_chi')->nullable()->after('so_dien_thoai');
            }

            if (!Schema::hasColumn('nguoi_dung', 'anh_dai_dien')) {
                $table->string('anh_dai_dien')->nullable()->after('dia_chi');
            }

            if (!Schema::hasColumn('nguoi_dung', 'vai_tro')) {
                $table->enum('vai_tro', ['admin', 'nhan_vien'])->default('nhan_vien')->after('anh_dai_dien');
            }

            if (!Schema::hasColumn('nguoi_dung', 'trang_thai')) {
                $table->enum('trang_thai', ['hoat_dong', 'tam_khoa'])->default('hoat_dong')->after('vai_tro');
            }

            if (!Schema::hasColumn('nguoi_dung', 'lan_dang_nhap_cuoi')) {
                $table->timestamp('lan_dang_nhap_cuoi')->nullable()->after('trang_thai');
            }

            if (!Schema::hasColumn('nguoi_dung', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            //
        });
    }
};