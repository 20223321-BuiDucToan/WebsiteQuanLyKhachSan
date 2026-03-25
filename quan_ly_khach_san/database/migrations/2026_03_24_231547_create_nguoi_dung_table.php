<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();
            $table->string('ho_ten', 100);
            $table->string('ten_dang_nhap', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('dia_chi')->nullable();
            $table->string('anh_dai_dien')->nullable();
            $table->string('vai_tro', 30)->default('nhan_vien');
            $table->string('trang_thai', 30)->default('hoat_dong');
            $table->timestamp('lan_dang_nhap_cuoi')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};