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
        Schema::create('notifications', function (Blueprint $table) {
            //$table->uuid('id')->primary();
            $table->increments('id')->comment('rekord azonosító');
            $table->string('type')->comment('Üzenet típusa');
            $table->morphs('notifiable');
            $table->text('data')->comment('Üzenet szövege');
            $table->timestamp('read_at')->nullable()->comment('Elolvasva ekkor');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
