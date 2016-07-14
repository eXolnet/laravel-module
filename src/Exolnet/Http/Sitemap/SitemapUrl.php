<?php namespace Exolnet\Http\Sitemap;
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
 * @package    Frontend
 * @subpackage Models
 * @author     eXolnet Inc. <info@exolnet.com>
 */

class SitemapUrl {
	const CHANGE_FREQUENCY_ALWAYS  = 'always';
	const CHANGE_FREQUENCY_HOURLY  = 'hourly';
	const CHANGE_FREQUENCY_DAILY   = 'daily';
	const CHANGE_FREQUENCY_WEEKLY  = 'weekly';
	const CHANGE_FREQUENCY_MONTHLY = 'monthly';
	const CHANGE_FREQUENCY_YEARLY  = 'yearly';
	const CHANGE_FREQUENCY_NEVER   = 'never';

	/**
	 * @var string
	 */
	protected $location;

	/**
	 * @var string
	 */
	protected $changeFrequency;

	/**
	 * @var string
	 */
	protected $priority;

	public function __construct($location)
	{
		$this->location = $location;
	}

	public function getLocation()
	{
		return $this->location;
	}

	public function setLocation($location)
	{
		$this->location = $location;

		return $this;
	}

	public function getChangeFrequency()
	{
		return $this->changeFrequency;
	}

	public function setChangeFrequency($changeFrequency)
	{
		$this->changeFrequency = $changeFrequency;

		return $this;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function setPriority($priority)
	{
		$this->priority = $priority;

		return $this;
	}

	public static function make($location)
	{
		return new SitemapUrl($location);
	}
}
