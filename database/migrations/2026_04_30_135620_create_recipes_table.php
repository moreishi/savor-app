<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->integer('servings')->default(4);
            $table->integer('prep_time')->nullable()->comment('minutes');
            $table->integer('cook_time')->nullable()->comment('minutes');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->string('image_url', 500)->nullable();
            $table->text('instructions');
            $table->text('tips')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
