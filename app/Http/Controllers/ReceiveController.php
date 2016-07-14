<?php namespace App\Http\Controllers;

use App\Lib\Airbrake;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\SelfLog;

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
	public function __construct( Request $request, SelfLog $_self_log ){		
		$this->_request = $request;
		//$this->_self_log = 
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


	/**
	 * ALIVE
	 * Will repond to requests checking if this application is alive, defined as running 
	 * every five minutes
	 *	 
	 * @param SelfLog $_self_log
	 * @return string
	 */
	public function alive( SelfLog $_self_log ){
		// Do we have a record written in the past 6 minutes? ( we allow one minute buffer to minimise false positives )
		$six_minutes_ago = strtotime( '6 minutes ago' );
		$last_record = $_self_log->orderBy('created_at', 'desc')->first();

		if( strtotime( $last_record->created_at ) < $six_minutes_ago )
			return 'down';

		return 'up';
	}
}





