<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_phong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_loai_phong', 20)->unique();
            $table->string('ten_loai_phong', 100)->unique();
            $table->text('mo_ta')->nullable();
            $table->decimal('gia_mot_dem', 12, 2);
            $table->unsignedInteger('so_nguoi_toi_da')->default(1);
            $table->decimal('dien_tich', 8, 2)->nullable();
            $table->unsignedInteger('so_giuong')->default(1);
            $table->string('loai_giuong', 50)->nullable();
            $table->unsignedInteger('so_phong_tam')->default(1);
            $table->boolean('co_ban_cong')->default(false);
            $table->boolean('co_bep_rieng')->default(false);
            $table->boolean('co_huong_bien')->default(false);
            $table->string('trang_thai', 30)->default('hoat_dong');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_phong');
    }
};