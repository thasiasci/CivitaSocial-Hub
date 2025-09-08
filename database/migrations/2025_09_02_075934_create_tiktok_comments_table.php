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
        Schema::create('tiktok_comments', function (Blueprint $table) {
            $table->id();
            $table->string('videoId')->nullable();
            $table->longText('authorProfileUrl')->nullable();
            $table->longText('authorProfileImageUrl')->nullable();
            $table->string('authorDisplayName')->nullable();
            $table->dateTime('publishedAt')->nullable();
            $table->longText('comment')->nullable();
            $table->string('sentimen')->nullable();
            $table->integer('likeCount')->nullable();
            $table->integer('replyCount')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiktok_comments');
    }
};
