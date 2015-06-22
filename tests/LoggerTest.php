<?php
use airshipwebservices\webmonitorclient\Logger;
 
class LoggerTest extends PHPUnit_Framework_TestCase {
 
	public function testWriteLog(){
		$params = [
			"body" => [
				"status" => FALSE, 
				"severity" => 50,
				"msg" => "This is an Error"
			],
			"app_id" => 1
		];

		$noCreate = $this->getMockBuilder('airshipwebservices\webmonitorclient\Logger')
						 ->setMethods(array('makeWebMonitorRequest'))
						 ->getMock();

		$fail_return_obj = new stdClass();
		$fail_return_obj->code = 200;
		$fail_return_obj->body = ['body'];

		$noCreate->expects( $this->once() )
				 ->method('makeWebMonitorRequest')
				 ->will( $this->returnValue($fail_return_obj) );

		$this->assertEquals( True, $noCreate->writeLog( $params ) );
	} 

	public function testWriteLogFailCode(){
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

	public function testWriteLogFailBody(){
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
		$fail_return_obj->code = 200;

		$noCreate->expects( $this->once() )
				 ->method('makeWebMonitorRequest')
				 ->will( $this->returnValue($fail_return_obj) );

		$result = $noCreate->writeLog( $params );

		$this->assertEquals( False, $result );
	}
}