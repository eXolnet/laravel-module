<?php namespace Exolnet\Database;
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
 * @subpackage Database
 * @author     eXolnet Inc. <info@exolnet.com>
 */

use Illuminate\Database\Query\Builder;

class QueryHelper {
	public static function toSql(Builder $query)
	{
		$sql = $query->toSql();
		$connection =  $query->getConnection();
		$pdo = $connection->getPdo();
		$bindings = $query->getBindings();
		$bindings = $connection->prepareBindings($bindings);
		if (!empty($bindings)) {
			foreach ($bindings as $binding) {
				$sql = preg_replace('/\?/', $pdo->quote($binding), $sql, 1);
			}
		}
		return $sql;
	}
}