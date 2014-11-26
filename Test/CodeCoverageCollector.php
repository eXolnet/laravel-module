<?php namespace Exolnet\Test;
/**
 * Copyright © 2014 eXolnet Inc. All rights reserved. (http://www.exolnet.com)
 *
 * This file contains copyrighted code that is the sole property of eXolnet Inc.
 * You may not use this file except with a written agreement.
 *
 * This code is distributed on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND,
 * EITHER EXPRESS OR IMPLIED, AND EXOLNET INC. HEREBY DISCLAIMS ALL SUCH
 * WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.
 *
 * @package    Exolnet
 * @subpackage Test
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use PHP_CodeCoverage;
use PHP_CodeCoverage_Filter;
use PHP_CodeCoverage_Report_PHP;

class CodeCoverageCollector
{
	private $rootDirectory;

	protected $coverage;

	/**
	 * @param      $rootDirectory
	 * @param null $name
	 */
	public function __construct($rootDirectory, $name = null)
	{
		$this->rootDirectory = $rootDirectory;
		$this->name = $name ?: 'Code Coverage (identified not set)';
	}

	public function start()
	{
		register_shutdown_function([$this, 'stop']);

		$filter = new PHP_CodeCoverage_Filter();
		$filter->addDirectoryToBlacklist($this->rootDirectory . "/vendor");
		$filter->addDirectoryToWhitelist($this->rootDirectory . "/app");
		$filter->addDirectoryToWhitelist($this->rootDirectory . "/src");

		$this->coverage = new PHP_CodeCoverage(null, $filter);
		$this->coverage->start($this->name);
	}

	public function stop()
	{
		$this->coverage->stop();

		$directory = $this->rootDirectory . '/app/storage/logs/code-coverage/';
		$filename = $directory.microtime(true).'.cov';
		if (!file_exists($directory)) {
			mkdir($directory, 0777, true);
		}

		$writer = new PHP_CodeCoverage_Report_PHP;
		$writer->process($this->coverage, $filename);
	}
}