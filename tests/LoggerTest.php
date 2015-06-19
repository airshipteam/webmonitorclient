<?php
 
use App\Http\Controllers\LoggerController;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
 
class LoggerTest extends TestCase{
 
	public function testLoggerSendsRequest(){
		$logger = new LoggerController;

		$params = [
			'body'	=> [ 
				'status' 		=> 1, 
				'severity' 		=> 50, 
				'msg' 			=> 'This is a test',
				'send_email'	=> 0, 				
			],	
			'app_id' => 4
		];	

		$result = $logger->writeLog( $params );
		$this->assertTrue( $result->code == 200 && $result->body != null );		
	} 
}