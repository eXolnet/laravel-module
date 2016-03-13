<?php namespace Exolnet\Test;

use Mockery as m;
use PHPUnit_Framework_TestCase;

abstract class TestCaseUnit extends PHPUnit_Framework_TestCase {
	public function tearDown()
	{
		m::close();
	}
}
