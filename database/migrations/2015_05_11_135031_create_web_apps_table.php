<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebAppsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		 Schema::create( 'web_apps' , function(Blueprint $table) {
            $table->increments( 'id' );
            $table->string( 'app_name' );                        
            $table->integer( 'run_schedule_id' );
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
		Schema::dropIfExists('web_apps');
	}
}
