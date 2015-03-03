<?php namespace Exolnet\Database;

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