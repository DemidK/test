<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transportation_orders', function (Blueprint $table) {
            $table->id();
            
            // Carrier Information
            $table->string('carrier_name')->nullable();
            $table->string('reg_number')->nullable();
            $table->string('address')->nullable();

            // Vehicle Information
            $table->boolean('is_our_vehicle')->default(false);
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('driver_name')->nullable();

            // Cargo Information
            $table->string('cargo_type')->nullable();
            $table->text('cargo_description')->nullable();
            $table->string('max_tonnage')->nullable();
            $table->string('ldm')->nullable();
            $table->integer('pallet_count')->nullable();
            $table->string('volume_m3')->nullable();

            // Load/Unload Information
            $table->string('load_address')->nullable();
            $table->text('load_address_info')->nullable();
            $table->dateTime('load_datetime')->nullable();
            $table->integer('adr_level')->nullable();

            $table->string('unload_address')->nullable();
            $table->text('unload_address_info')->nullable();
            $table->dateTime('unload_datetime')->nullable();

            // Financial Information
            $table->decimal('freight_amount', 10, 2)->nullable();
            $table->string('vat_status')->nullable();
            $table->string('currency')->default('EUR');
            $table->integer('payment_term_days')->default(30);
            $table->string('penalty_amount')->nullable();

            // Additional Information
            $table->text('documents_required')->nullable();
            $table->text('special_conditions')->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transportation_orders');
    }
}