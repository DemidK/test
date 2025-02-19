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
        Schema::create('nav_links', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Link text (e.g., "Home", "About")
            $table->string('url');  // Link URL (e.g., "/", "/about")
            $table->integer('position')->default(0); // Position in the navigation bar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_links');
    }
};
