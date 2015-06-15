<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunScheduleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		 Schema::create( 'run_schedules' , function(Blueprint $table) {
            $table->increments( 'id' );
            $table->text( 'rules' );           
            $table->timestamps();            
        } );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::dropIfExists('run_schedules');
	}
}
