<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExecutionColumnToWebApps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Schema::table('web_apps', function($table){

			$table->integer('max_execution_time_seconds');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Schema::table('web_apps', function($table){

			$table->dropColumn('max_execution_time_seconds');

		});
	}

}
