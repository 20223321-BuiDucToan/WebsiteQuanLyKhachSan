<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('su_dung_dich_vu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dat_phong_id')->constrained('dat_phong')->restrictOnDelete();
            $table->foreignId('dich_vu_id')->constrained('dich_vu')->restrictOnDelete();
            $table->unsignedInteger('so_luong')->default(1);
            $table->decimal('don_gia', 12, 2);
            $table->decimal('thanh_tien', 12, 2);
            $table->dateTime('thoi_diem_su_dung');
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('nguoi_dung')->nullOnDelete();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('su_dung_dich_vu');
    }
};