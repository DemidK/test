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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('new'); // e.g., new, in_progress, done, on_hold
            $table->string('priority')->default('medium'); // e.g., low, medium, high, critical
            $table->foreignId('user_id')->nullable()->comment('Assignee')->constrained('users')->onDelete('set null');
            $table->foreignId('creator_id')->comment('Creator')->constrained('users')->onDelete('cascade');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};