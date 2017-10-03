<?php namespace Exolnet\Test;

use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class TestCaseUnit extends TestCase {
	public function tearDown()
	{
		m::close();
	}
}
