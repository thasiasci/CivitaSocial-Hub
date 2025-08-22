<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('komentar_utama', function (Blueprint $table) {
            $table->string('sentimen')->nullable()->after('textOriginal');
        });
        Schema::table('komentar_balasan', function (Blueprint $table) {
            $table->string('sentimen')->nullable()->after('textOriginal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komentar_utama', function (Blueprint $table) {
            $table->dropColumn('sentimen');
        });
        Schema::table('komentar_balasan', function (Blueprint $table) {
            $table->dropColumn('sentimen');
        });
    }
};
