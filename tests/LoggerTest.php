<?php
use airshipwebservices\webmonitorclient\Logger;
 
class LoggerTest extends PHPUnit_Framework_TestCase {
 
	public function testTest(){
		$logger = new Logger;
		$this->assertTrue($logger->eatIt());
	} 
}