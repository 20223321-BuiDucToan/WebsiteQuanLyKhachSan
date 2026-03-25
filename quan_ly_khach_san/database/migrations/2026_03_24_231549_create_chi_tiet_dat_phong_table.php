<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_dat_phong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dat_phong_id')->constrained('dat_phong')->restrictOnDelete();
            $table->foreignId('phong_id')->constrained('phong')->restrictOnDelete();
            $table->decimal('gia_phong', 12, 2);
            $table->unsignedInteger('so_dem')->default(1);
            $table->unsignedInteger('so_nguoi_lon')->default(1);
            $table->unsignedInteger('so_tre_em')->default(0);
            $table->dateTime('ngay_nhan_phong_thuc_te')->nullable();
            $table->dateTime('ngay_tra_phong_thuc_te')->nullable();
            $table->string('trang_thai', 30)->default('da_dat');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_dat_phong');
    }
};