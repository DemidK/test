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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->json('items')->comment('Array of objects with description, quantity, price, vat, total');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_vat')->nullable();
            $table->text('customer_address')->nullable();
            $table->text('customer_post_address')->nullable();
            $table->string('updater')->nullable();
            $table->decimal('total_vat', 10, 2)->default(0);
            $table->decimal('total_wo_vat', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};