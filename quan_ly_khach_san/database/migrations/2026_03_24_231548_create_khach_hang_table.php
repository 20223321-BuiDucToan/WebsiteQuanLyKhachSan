<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khach_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khach_hang', 20)->unique();
            $table->string('ho_ten', 100);
            $table->string('gioi_tinh', 20)->nullable();
            $table->date('ngay_sinh')->nullable();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('so_giay_to', 50)->nullable();
            $table->string('loai_giay_to', 30)->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('quoc_tich', 50)->nullable();
            $table->string('hang_khach_hang', 30)->default('thuong');
            $table->string('trang_thai', 30)->default('hoat_dong');
            $table->string('anh_dai_dien')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khach_hang');
    }
};