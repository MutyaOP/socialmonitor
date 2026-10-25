<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 7)->default('#6366f1');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // one-to-many: санат → кілт сөздер (классификация сөздігі)
        Schema::create('keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('word', 60);                         // сөз түбірі
            $table->unsignedTinyInteger('weight')->default(1);  // 1..3
            $table->timestamps();
            $table->unique(['category_id', 'word']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('categories');
    }
};
