<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model {	

	/**
	 * Foreign key for web app relation
	 *
	 * @var string
	 */
	protected $web_app_foreign_key = 'web_app_id';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['status', 'web_app_id', 'severity', 'msg', 'send_email'];

	/**
	 * WEB APP
	 * Will return web app model that this log model belongs to
	 *
	 * @return Log 
	 */
	public function webApp(){
		return $this->belongsTo( 'App\Models\WebApp', $this->web_app_foreign_key );
	}
}
