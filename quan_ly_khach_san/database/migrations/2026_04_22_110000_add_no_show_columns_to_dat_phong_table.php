<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dat_phong', function (Blueprint $table) {
            $table->dateTime('thoi_diem_khong_den')->nullable()->after('ngay_tra_phong_thuc_te');
            $table->decimal('phi_khong_den', 12, 2)->default(0)->after('thoi_diem_khong_den');
            $table->text('ly_do_khong_den')->nullable()->after('phi_khong_den');

            $table->index(['trang_thai', 'ngay_nhan_phong_du_kien'], 'dat_phong_trang_thai_ngay_nhan_index');
        });
    }

    public function down(): void
    {
        Schema::table('dat_phong', function (Blueprint $table) {
            $table->dropIndex('dat_phong_trang_thai_ngay_nhan_index');
            $table->dropColumn([
                'thoi_diem_khong_den',
                'phi_khong_den',
                'ly_do_khong_den',
            ]);
        });
    }
};
