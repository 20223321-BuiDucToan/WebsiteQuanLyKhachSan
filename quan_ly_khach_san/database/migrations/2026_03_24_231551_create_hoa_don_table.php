<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id();
            $table->string('ma_hoa_don', 20)->unique();
            $table->foreignId('dat_phong_id')->constrained('dat_phong')->restrictOnDelete();
            $table->decimal('tong_tien_phong', 12, 2)->default(0);
            $table->decimal('tong_tien_dich_vu', 12, 2)->default(0);
            $table->decimal('giam_gia', 12, 2)->default(0);
            $table->decimal('thue', 12, 2)->default(0);
            $table->decimal('tong_tien', 12, 2);
            $table->string('trang_thai', 30)->default('chua_thanh_toan');
            $table->dateTime('thoi_diem_xuat')->nullable();
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('nguoi_dung')->nullOnDelete();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};