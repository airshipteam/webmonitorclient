<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RunSchedule extends Model {	

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'run_schedules';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['rules'];

	/**
	 * WEB APP
	 * Will return web app model that this log model belongs to
	 *
	 * @return Log 
	 */
	public function webApp(){
		return $this->belongsTo( 'App\Models\WebApp' );
	}

	/**
	 * GET ALIVE INTERVAL
	 * Will return the alive interval in seconds
	 *
	 * @return int 
	 */
	public function getAliveInterval(){		
		sscanf( $this->rules, "%d:%d:%d", $hours, $minutes, $seconds );
		$seconds = isset( $seconds ) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
		return $seconds;
	}
}