<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->string('nguon_tao', 30)->default('noi_bo')->after('trang_thai');
            $table->string('ma_tham_chieu', 100)->nullable()->after('phuong_thuc_thanh_toan');
            $table->foreignId('nguoi_xu_ly_id')->nullable()->after('nguoi_tao_id')->constrained('nguoi_dung')->nullOnDelete();
            $table->dateTime('thoi_diem_xu_ly')->nullable()->after('nguoi_xu_ly_id');

            $table->index(['nguon_tao', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->dropIndex(['nguon_tao', 'trang_thai']);
            $table->dropConstrainedForeignId('nguoi_xu_ly_id');
            $table->dropColumn([
                'nguon_tao',
                'ma_tham_chieu',
                'thoi_diem_xu_ly',
            ]);
        });
    }
};
