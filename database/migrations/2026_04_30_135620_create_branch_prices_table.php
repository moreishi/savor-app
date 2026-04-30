<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('ingredient_id')->constrained();
            $table->decimal('price', 10, 2);
            $table->string('variant_label', 255)->nullable();
            $table->decimal('purchase_quantity', 10, 2)->default(1);
            $table->string('purchase_unit', 50)->default('pcs');
            $table->boolean('is_on_promo')->default(false);
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->string('promo_label', 255)->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'ingredient_id', 'variant_label'], 'branch_ingredient_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_prices');
    }
};
