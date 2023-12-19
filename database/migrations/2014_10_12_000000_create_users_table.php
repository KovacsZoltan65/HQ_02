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
            $table->id()->comment('Rekord azonosító');
            $table->string('name')->comment('Felhasználó neve');
            $table->string('email')->unique()->comment('Email cím');
            $table->timestamp('email_verified_at')->nullable()->comment('Email ellenőrzés dátuma');
            $table->string('password')->comment('Jelszó');
            $table->string('language', 2)->default('hu')->comment('Felhasználó nyelve');
            $table->rememberToken();
            $table->timestamps();
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
