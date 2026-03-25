<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dat_phong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_dat_phong', 20)->unique();
            $table->foreignId('khach_hang_id')->constrained('khach_hang')->restrictOnDelete();
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('nguoi_dung')->nullOnDelete();
            $table->dateTime('ngay_dat');
            $table->date('ngay_nhan_phong_du_kien');
            $table->date('ngay_tra_phong_du_kien');
            $table->dateTime('ngay_nhan_phong_thuc_te')->nullable();
            $table->dateTime('ngay_tra_phong_thuc_te')->nullable();
            $table->unsignedInteger('so_nguoi_lon')->default(1);
            $table->unsignedInteger('so_tre_em')->default(0);
            $table->string('trang_thai', 30)->default('cho_xac_nhan');
            $table->string('nguon_dat', 30)->default('truc_tiep');
            $table->text('yeu_cau_dac_biet')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dat_phong');
    }
};