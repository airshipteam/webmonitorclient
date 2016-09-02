<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::post('/msg/{id}', [
	'as' => 'msg',
	'uses' => 'ReceiveController@msg'
]);

Route::get('/alive', [
	'as' => 'alive',
	'uses' => 'ReceiveController@alive'
]);

Route::group(['prefix' => '/pings'],function(){

	Route::post('/start', ['as' => 'ping.start', 'uses' => 'PingController@start']);

	Route::post('/end', ['as' => 'ping.end', 'uses' => 'PingController@end']);

});


Route::post('/test', function(\Illuminate\Http\Request $request){


	\App\Lib\Airbrake::send($request->all());

});


//Route::post('/alive/{id}', 'AliveController@index');


//Route::get('/', 'WelcomeController@index');

//Route::get('home', 'HomeController@index');

/*Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);*/
