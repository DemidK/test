<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPgCredentialsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('pg_username')->nullable();
            $table->text('pg_password')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['pg_username', 'pg_password']);
        });
    }
}