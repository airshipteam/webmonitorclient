<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebApp extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'web_apps';

	/**
	 * Foreign key for log relationships
	 *
	 * @var string
	 */
	protected $log_foreign_key = 'web_app_id';

	/**
	 * Foreign jey for run schedule relationships
	 *
	 * @var string
	 */
	protected $run_schedule_foreign_key = 'id';

	/**
	 * Local key for run schedule relationships
	 *
	 * @var string
	 */
	protected $run_schedule_local_key = 'run_schedule_id';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [ 'app_name', 'run_schedule_id' ];

	/**
	 * RUN SCHEDULE
	 * Will return the run schedule ( if present ) for this model
	 *
	 * @return RunSchedule 
	 */
	public function runSchedule(){
		return $this->hasOne( 'App\Models\RunSchedule', $this->run_schedule_foreign_key, $this->run_schedule_local_key );
	}

	/**
	 * LOGS
	 * Will return the logs ( of any ) for this model
	 *
	 * @return Log 
	 */
	public function logs(){
		return $this->hasMany( 'App\Models\Log', $this->log_foreign_key );
	}

}
