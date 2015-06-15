<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		 Schema::create( 'logs' , function(Blueprint $table) {
            $table->increments( 'id' );
            $table->boolean( 'status' );
            $table->integer( 'web_app_id' );
            $table->integer('severity');
            $table->text( 'msg' );
            $table->timestamps();
            $table->softDeletes();
        } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::dropIfExists('logs');
	}
}
