<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phong', function (Blueprint $table) {
            $table->text('anh_phong')->nullable()->after('ghi_chu');
        });
    }

    public function down(): void
    {
        Schema::table('phong', function (Blueprint $table) {
            $table->dropColumn('anh_phong');
        });
    }
};
