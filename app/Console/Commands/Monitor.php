<?php namespace App\Console\Commands;

use App\Lib\Airbrake;
use Illuminate\Console\Command;
use App\Models\WebApp;
use App\Models\SelfLog;

use Illuminate\Contracts\Mail\Mailer;

class Monitor extends Command {

	/*----------------------
	// 
	// LITERALS
	//
	//----------------------*/
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'monitor';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks monitored apps for alive status and error messages';

	/**
	 * The not alive severity
	 *
	 * @var int
	 */
	protected $not_alive_severity = 50;

	/**
	 * The not alive msg
	 *
	 * @var int
	 */
	protected $not_alive_msg = 'The app has failed to send an alive message.';

	/**
	 * Send email
	 *
	 * @var int
	 */
	protected $send_email = 1;

	/**
	 * Email sent
	 *
	 * @var int
	 */
	protected $email_sent = 0;

	/**
	 * Email subject
	 *
	 * @var int
	 */
	protected $email_subject = 'Web Monitor Alert';
	protected $email_subject_app_name = '';

	/**
	 * A buffer of two minutes to add to any last alive checks to stop error beind fired
	 * if we run a second or two late.
	 *
	 * @var int
	 */
	protected $last_alive_buffer = 120;

	/**
	 * Check alive window
	 * - Will define the time window that alerts will be sent if no alive message has been sent.
	 *   This is to stop error flooding - so we should only get one or two errors when an app hasn't 
	 *   sent any alive message.
	 * - Currently set at 10 minutes
	 *
	 * @var int
	 */
	protected $last_alive_window = 600;

	/*----------------------
	// 
	// VARS
	//
	//----------------------*/
	/**
	 * The current runtime timestamp
	 *
	 * @var int
	 */
	protected $now;

	/**
	 * Mail address to send alert emails to
	 *
	 * @var string
	 */
	protected $mail_send_to;

	/**
	 * Name of person we are sending the alert emails to.
	 *
	 * @var string
	 */
	protected $send_to_name;

	/*----------------------
	// 
	// COLLECTIONS
	//
	//----------------------*/
	/**
	 * Web apps to monitor collection
	 *
	 * @var Collection
	 */
	protected $_web_apps;

	/*----------------------
	// 
	// MODELS
	//
	//----------------------*/
	/**
	 * WebApp Model
	 *
	 * @var WebApp
	 */
	protected $_web_app;

	/**
	 * Latest log model
	 *
	 * @var Log
	 */
	protected $_latest_log;

	/**
	 * Self log model
	 *
	 * @var SelfLog
	 */
	protected $_self_log;

	/*----------------------
	// 
	// OBJECTS
	//
	//----------------------*/
	/**
	 * Mail Object
	 *
	 * @var Mailer
	 */
	protected $_mail;

	/**
	 * CONSTRUCTOR
	 * Gets instance of WebApp model
	 * Sets the runtime timestamp
	 *
	 * @var WebApp $_Web_app
 	 * @var Mailer $_Mail
	 * @var SelfLog $_Self_Log
	 * @return void
	 */
	public function __construct( WebApp $_Web_app, Mailer $_Mail, SelfLog $_Self_log ){		
		parent::__construct();

		$this->_web_app = $_Web_app;
		$this->_mail = $_Mail;
		$this->_self_log = $_Self_log;

		$this->now = time();

		$this->mail_send_to = config('app.mail_send_to');
		$this->send_to_name = config('app.send_to_name');
	}

	/**
	 * FIRE
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire(){

			$this->alive();
			$this->getWebAppsCollection();
			$this->doChecks();

	}

	/**
	 * ALIVE
	 * First thing we do is write to the database to confirm we are alive
	 *
	 * @return void
	 */
	protected function alive(){
		$self_log = ['status' => 1];
		$this->_self_log->create( $self_log );
	}

	/**
	 * GET WEB APPS COLLECTION
	 * Will get and store collection of web apps that we are monitoring
	 *	 
	 * @return void
	 */
	protected function getWebAppsCollection(){
		$this->_web_apps = $this->_web_app->all();
	}

	/**
	 * DO CHECKS
	 * Will iterate through the app collection and perform neccessary checks
	 *	
	 * @return void
	 */	
	protected function doChecks(){

		$this->_web_apps->each( function( $_web_app ) {		
			$this->checkLogAlerts( $_web_app );

			if( !$this->checkLastAlive( $_web_app ) )
				$this->sendNotAliveMsg( $_web_app );			
		});
	}

	/**
	 * CHECK LOG EMAILS
	 * Will check against the database for the passed app to see if we have any log messages that nned an  email sent
	 *	
	 * @param WebApp $_web_app
	 * @return void 
	 */	
	protected function checkLogAlerts( $_web_app ){
		$alerts = $_web_app->logs()->where('send_email', '=', $this->send_email )->get();
		if( $alerts->count() !== 0 )
			foreach( $alerts as $_log ){
				$this->sendLogMsg( $_log );				
				$this->markEmailSent( $_log );
			}		
	}

	/**
	 * CHECK LAST ALIVE
	 * Will check that we have received an alive message for this app within the run schedule specified
	 * time interval
	 *	
	 * @param WebApp $_web_app
	 * @return boolean 
	 */	
	protected function checkLastAlive( $_web_app ){

		$_run_schedule = $_web_app->runSchedule;
		
		if($_run_schedule->id === 0)
			return true;

		$seconds_interval = $_run_schedule->getAliveInterval();
		$this->setLatestLog( $_web_app );	

		if( $this->_latest_log )			
			if( strtotime( $this->_latest_log->updated_at ) < ( $this->now - ( $seconds_interval + $this->last_alive_buffer ) ) ){				      					
				$time_since_last_log = $this->now - strtotime( $this->_latest_log->updated_at );

				if( $time_since_last_log > ($seconds_interval + $this->last_alive_window) )
					return true;
				else			
					return false;				
			}
	
		return true;
	}

	/**
	 * MARK SENT
	 * Will set a log record to having it's email sent
	 *	
	 * @param Log $_log
	 * @return boolean 
	 */	
	protected function markEmailSent( $_log ){
		$_log->send_email = $this->email_sent;
		$_log->save();
	}

	/**
	 * SET LATEST LOG
	 * Will set the last log message for this web app
	 *	
	 * @param WebApp $_web_app
	 * @return void 
	 */	
	protected function setLatestLog( $_web_app ){
		$this->_latest_log = $_web_app->logs()->orderBy('id', 'desc')->first();
	}

	/**
	 * DATE TO TIMESTAMP
	 * Will convert a Datetime to a timestamp
	 *	
	 * @param string $last_log_date
	 * @return int 
	 */	
	protected function dateToTimestamp( $last_log_time ){
		return strtotime( $last_log_time );
	}

	/**
	 * NOT ALIVE MSG
	 * Will send an email for a log message
	 *	
	 * @param Log $_log
	 * @return void 
	 */	
	protected function sendNotAliveMsg( $_web_app ){
		$msg = $this->not_alive_msg . ' The last alive message was recieved at: ' . $this->_latest_log->updated_at;

		$email_data = array(
			'severity' 	=> $this->not_alive_severity,
			'msg' 		=> $msg,
			'app_name' 	=> $_web_app->app_name,
			'app_id' 	=> $_web_app->id
		);

		$this->sendEmail( $email_data );
	}

	/**
	 * SEND LOG MSG
	 * Will send an email for a log message
	 *	
	 * @param Log $_log
	 * @return void 
	 */	
	protected function sendLogMsg( $_log ){
		$web_app_name = $this->getWebAppName( $_log );

		$email_data = array(
			'severity' 	=> $_log->severity,
			'msg' 		=> $_log->msg,
			'app_name' 	=> $web_app_name,
			'app_id' 	=> $_log->web_app_id
		);

		$this->sendEmail( $email_data );
	}

	/**
	 * SEND MAIL
	 * Will send an email populated wth data from the passed Log model
	 *	
	 * @param array $email_data
	 * @return boolean 
	 */	
	protected function sendEmail( $email_data ){

		$input = $email_data;

		$input['web_app_id'] = $input['app_id'];

		Airbrake::send($input);

		$this->email_subject_app_name = $this->email_subject  . ' - ' . $email_data['app_name'];
		$this->_mail->send('emails.error', $email_data, function($message){
		    $message->to( $this->mail_send_to, $this->send_to_name )->subject( $this->email_subject_app_name);
		});
	}

	/**
	 * GET WEB APP NAME
	 * Given a log model, this mehtod will return the associated web app name
	 *	
	 * @param Log $_log
	 * @return string 
	 */	
	protected function getWebAppName( $_log ){
		return $_log->find( $_log->id )->webApp->app_name;
	}


}






