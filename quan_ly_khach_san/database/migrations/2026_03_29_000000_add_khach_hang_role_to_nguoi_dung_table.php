<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('nguoi_dung')) {
            return;
        }

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE nguoi_dung
                MODIFY COLUMN vai_tro ENUM('admin', 'nhan_vien', 'khach_hang')
                NOT NULL DEFAULT 'nhan_vien'"
            );
        }
    }

    public function down(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('nguoi_dung')) {
            return;
        }

        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::table('nguoi_dung')
                ->where('vai_tro', 'khach_hang')
                ->update(['vai_tro' => 'nhan_vien']);

            DB::statement(
                "ALTER TABLE nguoi_dung
                MODIFY COLUMN vai_tro ENUM('admin', 'nhan_vien')
                NOT NULL DEFAULT 'nhan_vien'"
            );
        }
    }
};
