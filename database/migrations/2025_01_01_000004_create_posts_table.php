<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('region', 60);
            $table->dateTime('published_at')->index();
            // талдау нәтижесі
            $table->decimal('sentiment_score', 4, 2)->default(0);   // -1..1
            $table->string('sentiment', 20)->default('neutral');
            $table->unsignedTinyInteger('severity_score')->default(0); // 0..100
            $table->string('severity', 20)->default('low');
            $table->json('top_words')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();
        });

        // many-to-many: мәтін ⇄ санат (талдау салмағымен)
        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('score')->default(0);
            $table->string('matched')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unique(['category_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('posts');
    }
};
