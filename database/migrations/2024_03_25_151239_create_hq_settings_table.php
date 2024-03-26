<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('hq_settings', function(Blueprint $table) {
                $table->increments('id')->comment('Rekord azonosító');
                $table->string('key')->comment('kulcs');
                $table->string('value')->comment('érték');

                $table->timestamps();
                $table->softDeletes();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hq_settings');
	}
};
