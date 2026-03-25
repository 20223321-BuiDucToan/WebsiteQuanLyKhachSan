<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dich_vu', function (Blueprint $table) {
            $table->id();
            $table->string('ma_dich_vu', 20)->unique();
            $table->string('ten_dich_vu', 100);
            $table->string('loai_dich_vu', 50)->nullable();
            $table->string('don_vi_tinh', 30)->default('lan');
            $table->decimal('don_gia', 12, 2);
            $table->text('mo_ta')->nullable();
            $table->string('trang_thai', 30)->default('hoat_dong');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dich_vu');
    }
};