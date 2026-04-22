<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('khach_hang', function (Blueprint $table) {
            $table->foreignId('nguoi_dung_id')
                ->nullable()
                ->after('id')
                ->constrained('nguoi_dung')
                ->nullOnDelete();
        });

        $daLienKet = [];

        $danhSachKhachHang = DB::table('khach_hang')
            ->select('id', 'email', 'so_dien_thoai')
            ->orderBy('id')
            ->get();

        foreach ($danhSachKhachHang as $khachHang) {
            $nguoiDungId = null;

            if (! empty($khachHang->email)) {
                $nguoiDungId = DB::table('nguoi_dung')
                    ->where('vai_tro', 'khach_hang')
                    ->where('email', $khachHang->email)
                    ->whereNotIn('id', $daLienKet)
                    ->value('id');
            }

            if (! $nguoiDungId && ! empty($khachHang->so_dien_thoai)) {
                $nguoiDungId = DB::table('nguoi_dung')
                    ->where('vai_tro', 'khach_hang')
                    ->where('so_dien_thoai', $khachHang->so_dien_thoai)
                    ->whereNotIn('id', $daLienKet)
                    ->value('id');
            }

            if (! $nguoiDungId) {
                continue;
            }

            DB::table('khach_hang')
                ->where('id', $khachHang->id)
                ->update(['nguoi_dung_id' => $nguoiDungId]);

            $daLienKet[] = $nguoiDungId;
        }

        Schema::table('khach_hang', function (Blueprint $table) {
            $table->unique('nguoi_dung_id');
        });
    }

    public function down(): void
    {
        Schema::table('khach_hang', function (Blueprint $table) {
            $table->dropUnique(['nguoi_dung_id']);
            $table->dropConstrainedForeignId('nguoi_dung_id');
        });
    }
};
