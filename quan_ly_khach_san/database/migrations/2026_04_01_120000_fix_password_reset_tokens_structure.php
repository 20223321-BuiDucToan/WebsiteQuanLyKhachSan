<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->string('email')->nullable()->index()->after('id');
            }

            if (!Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->string('token')->nullable()->after('email');
            }

            if (!Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->dropColumn('token');
            }

            if (Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
