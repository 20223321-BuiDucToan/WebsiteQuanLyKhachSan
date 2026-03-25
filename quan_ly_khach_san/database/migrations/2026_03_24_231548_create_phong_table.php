<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phong', 20)->unique();
            $table->string('so_phong', 20)->unique();
            $table->foreignId('loai_phong_id')->constrained('loai_phong')->restrictOnDelete();
            $table->unsignedInteger('tang')->nullable();
            $table->string('trang_thai', 30)->default('trong');
            $table->string('tinh_trang_ve_sinh', 30)->default('sach');
            $table->string('tinh_trang_hoat_dong', 30)->default('hoat_dong');
            $table->decimal('gia_mac_dinh', 12, 2)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong');
    }
};