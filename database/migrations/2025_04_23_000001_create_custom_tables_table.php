// database/migrations/2024_02_23_000001_create_custom_tables_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Table name in the database
            $table->string('display_name');    // Human-readable name
            $table->string('description')->nullable();
            $table->json('fields');            // Field definitions
            $table->json('validation_rules')->nullable();  // Laravel validation rules
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_tables');
    }
};