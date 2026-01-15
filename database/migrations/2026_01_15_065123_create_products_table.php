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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Basic info
            $table->string('name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Pricing
            $table->integer('price'); // amount in cents (e.g. 1500 = $15.00)*-
            $table->string('currency', 3)->default('USD');

            // Stripe mapping
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();

            // Control
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
