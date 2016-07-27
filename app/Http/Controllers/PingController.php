<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Lib\Pingdom;
use App\Models\WebApp;
use App\Ping;
use Illuminate\Http\Request;

class PingController extends Controller {

	public function start(Request $request)
	{

		$validation = \Validator::make($request->all(), [
			'app_id' => 'required',
			'time_sent' => 'required'
		]);

		if($validation->fails())
		{
			return response()->json($validation->errors(), 400);
		}

		$app = WebApp::find($request->input('app_id'));

		if(!$app)
		{
			return response()->json([], 404);
		}

		$ping = Ping::create([
			'web_app_id' => $app->id,
			'status' => 'pending',
			'time_sent' => $request->input('time_sent'),
			'type' => 'start',
			'run_id' => Pingdom::createRunID($app)
		]);


		if(!$ping)
		{
			return response()->json([], 400);
		}

		return response()->json($ping, 200);

	}

	public function end(Request $request)
	{

		$validation = \Validator::make($request->all(), [
			'time_sent' => 'required',
			'run_id' => 'required'
		]);

		if($validation->fails())
		{
			return response()->json($validation->errors(), 400);
		}

		$statusUpdated = Pingdom::completeRun($request->all());

		if($statusUpdated)
		{
			return response()->json([], 200);
		}

		return response()->json([], 500);

	}




}
