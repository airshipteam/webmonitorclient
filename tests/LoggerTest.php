<?php
use airshipwebservices\webmonitorclient\Logger;
 
class LoggerTest extends PHPUnit_Framework_TestCase {

	public function __construct(){
		parent::__construct();

		$this->mockLogger = $this->getMockBuilder( 'airshipwebservices\webmonitorclient\Logger' )
						 ->setMethods( array('makeWebMonitorRequest') )
						 ->getMock();

		$this->params = [
			"body" => [
				"status" => FALSE, 
				"severity" => 50,
				"msg" => "This is an Error"
			],
			"app_id" => 1
		];
	}
 
	public function testWriteLog(){
		$fail_return_obj = new stdClass();
		$fail_return_obj->code = 200;
		$fail_return_obj->body = ['body'];
		
		$this->mockLogger->expects( $this->once() )
				 		 ->method( 'makeWebMonitorRequest' )
				 		 ->will( $this->returnValue($fail_return_obj) );

		$this->assertEquals( True, $this->mockLogger->writeLog( $this->params ) );
	} 

	public function testWriteLogFailCode(){
		$fail_return_obj = new stdClass();
		$fail_return_obj->code = 500;
		$fail_return_obj->body = ['body'];

		$this->mockLogger->expects( $this->once() )
				 		 ->method( 'makeWebMonitorRequest' )
				 		 ->will( $this->returnValue($fail_return_obj) );

		$this->assertEquals( False, $this->mockLogger->writeLog( $this->params ) );
	}

	public function testWriteLogFailBody(){
		$fail_return_obj = new stdClass();
		$fail_return_obj->code = 200;

		$this->mockLogger->expects( $this->once() )
				 		 ->method( 'makeWebMonitorRequest' )
				 		 ->will( $this->returnValue($fail_return_obj) );

		$this->assertEquals( False, $this->mockLogger->writeLog( $this->params ) );
	}
}