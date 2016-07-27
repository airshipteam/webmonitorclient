<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type');
			$table->string('run_id');
			$table->string('status');
			$table->integer('web_app_id');
			$table->string('time_sent');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pings');
	}

}
