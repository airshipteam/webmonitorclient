<?php namespace airshipwebservices\webmonitorclient;

use Unirest;

class Logger{

	/**********************************
	 *
	 * LOGGER
	 * Class to talk to the web monitor
	 *
	 */

	/*----------------------
	// 
	// LITERALS
	//
	//----------------------*/
	/**
	 * Application status good
	 *
	 * @var int
	 */
	protected $app_status_good = 1;

	/**
	 * Application status bad
	 *
	 * @var int
	 */
	protected $app_status_bad = 0;	

	/**
	 * Don't Send Email
	 *
	 * @var int
	 */
	protected $send_email = 1;

	/**
	 * Send Email
	 *
	 * @var int
	 */
	protected $dont_send_email = 0;	

	/**
	 * Default route to use
	 *
	 * @var string
	 */
	protected $default_route = '/msg';	

	/*----------------------
	// 
	// VARS
	//
	//----------------------*/
	/**
	 * Web monitor host
	 *
	 * @var string
	 */
	protected $web_monitor_host = false;

	/**
	 * Web monoitor app id
	 *
	 * @var int
	 */
	protected $web_monitor_app_id = false;

	/**
	 * Web monitor headers
	 *
	 * @var array
	 */
	protected $web_monitor_headers = array(
		"Accept" => "application/json"
	);

	/**
	 * CONSTRUCT
	 *
	 * @return void
	 */
	public function __construct(){		
		$this->web_monitor_host = "http://webmonitor.local.airship.co.uk";
	}

	public function eatIt(){
		return true;
	}

	/**
	 * WRITE LOG
	 * Entry point for sending a request message to the web monitor
	 *
	 * @var array $params
	 * @return boolean
	 */
	public function writeLog( $params ){
		$params['route'] = isset( $params['route'] ) ? $params['route'] : $this->default_route;
		return $this->makeWebMonitorRequest( $params );
	}

	/**
	 * MAKE WEB MONITOR REQUEST
	 * Will send a request to the web monitor
	 *
	 * @var array $params
	 * @return mixed
	 */
	protected function makeWebMonitorRequest( $params ){
		$response = Unirest\Request::post( 
			$this->web_monitor_host . $params['route'] . '/' . $params['app_id'], 
			$this->web_monitor_headers, 
			$params['body']
		);	

		return $response;
	}
}