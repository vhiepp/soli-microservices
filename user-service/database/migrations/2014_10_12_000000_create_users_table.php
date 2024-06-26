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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fullname', 100);
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('uid', 50);
            $table->bigInteger('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('role', ['user', 'admin']);
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
