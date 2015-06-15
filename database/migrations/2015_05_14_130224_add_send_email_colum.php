<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendEmailColum extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		Schema::table( 'logs' , function(Blueprint $table) {
			$table->boolean('send_email');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::table( 'logs' , function(Blueprint $table) {
			$table->dropColumn('send_email');
		});
	}
}
