<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class ReceiveController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Alive Controller
	|--------------------------------------------------------------------------
	|
	| This controller receives an alive request or 'ping' from an application. It
	| will then take the appropriate action dependant on the payload in the POST of
	| the request
	|
	*/

	/**
	 * The Request object
	 *
	 * @var Request
	 */
	protected $_request;

	/**
	 * The Log model
	 *
	 * @var Log
	 */
	protected $_model;

	/**
	 * The app id making the request
	 *
	 * @var int 
	 */
	protected $id;

	/**
	 * The request array
	 *
	 * @var Array
	 */
	protected $input;

	/**
	 * Constructor for this controller.
	 * Get an instance of Request
	 *
	 * @return void
	 */
	public function __construct( Request $request ){		
		$this->_request = $request;
	}

	/**
	 * MSG
	 * Will receive a msg from a web app and write a new log record
	 *
	 * @param int $id
	 * @param Log $_log
	 * @return Response
	 */
	public function msg( $id, Log $_log ){
		$this->input = $this->_request->all();
		$this->id = $id;			
		$this->input['web_app_id'] = $this->id;

		$this->_log = $_log->create( $this->input );	

		return response()->json( $this->_log->toArray() );		
	}
}





