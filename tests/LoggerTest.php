<?php
use airshipwebservices\webmonitorclient\Logger;
 
class LoggerTest extends PHPUnit_Framework_TestCase {
 
	public function testWriteLog(){
		$logger = new Logger;

		$params = [
			"body" => [
				"status" => FALSE, 
				"severity" => 50,
				"msg" => "This is an Error"
			],
			"app_id" => 1
		];

		$this->assertEquals( True, $logger->writeLog( $params ) );
	} 

	public function testWriteLogFail(){
		$params = [
			"body" => [
				"status" => FALSE, 
				"severity" => 50,
				"msg" => "This is an Error"
			],
			"app_id" => 21
		];

		$noCreate = $this->getMockBuilder('airshipwebservices\webmonitorclient\Logger')
						 ->setMethods(array('makeWebMonitorRequest'))
						 ->getMock();

		$fail_return_obj = new stdClass();
		$fail_return_obj->code = 500;

		$noCreate->expects( $this->once() )
				 ->method('makeWebMonitorRequest')
				 ->will( $this->returnValue($fail_return_obj) );

		$result = $noCreate->writeLog( $params );

		$this->assertEquals( False, $result );
	}
}