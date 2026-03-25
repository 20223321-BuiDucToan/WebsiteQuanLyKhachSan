<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->string('ma_thanh_toan', 20)->unique();
            $table->foreignId('hoa_don_id')->constrained('hoa_don')->restrictOnDelete();
            $table->decimal('so_tien', 12, 2);
            $table->string('phuong_thuc_thanh_toan', 50);
            $table->dateTime('thoi_diem_thanh_toan');
            $table->string('trang_thai', 30)->default('thanh_cong');
            $table->foreignId('nguoi_tao_id')->nullable()->constrained('nguoi_dung')->nullOnDelete();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};