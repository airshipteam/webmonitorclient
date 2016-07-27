<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Ping extends Model {

	protected $fillable = ['type', 'web_app_id', 'time_sent', 'run_id', 'status'];

}
