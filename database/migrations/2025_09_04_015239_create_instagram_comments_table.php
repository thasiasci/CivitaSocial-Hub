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
        Schema::create('instagram_comments', function (Blueprint $table) {
            $table->id();
            $table->string('link_konten');
            $table->string('periode')->nullable();
            $table->text('comment');
            $table->string('sentimen')->nullable();
            $table->boolean('is_spam')->nullable();
            $table->string('bulan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_comments');
    }
};
