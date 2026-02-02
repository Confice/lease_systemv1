<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'themePreference')) {
                $table->string('themePreference', 20)->default('light')->after('isFirstLogin');
            }
            if (!Schema::hasColumn('users', 'reduceMotion')) {
                $table->boolean('reduceMotion')->default(false)->after('themePreference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reduceMotion')) {
                $table->dropColumn('reduceMotion');
            }
            if (Schema::hasColumn('users', 'themePreference')) {
                $table->dropColumn('themePreference');
            }
        });
    }
};
