<?php namespace Exolnet\Database;

use DB;
use Illuminate\Database\Query\Builder;

class QueryHelper
{
	/**
	 * @param \Illuminate\Database\Query\Builder $query
	 * @param string                             $alias
	 * @return \Illuminate\Database\Query\Expression
	 */
	public static function subQuery(Builder $query, $alias)
	{
		return DB::raw('(' . $query->toSql() . ') as ' . $alias);
	}

	/**
	 * @param \Illuminate\Database\Query\Builder $query
	 * @return string
	 */
	public static function toSql(Builder $query)
	{
		$sql = $query->toSql();
		$connection = $query->getConnection();
		$pdo = $connection->getPdo();
		$bindings = $query->getBindings();
		$bindings = $connection->prepareBindings($bindings);
		if ( ! empty($bindings)) {
			foreach ($bindings as $binding) {
				$sql = preg_replace('/\?/', $pdo->quote($binding), $sql, 1);
			}
		}
		return $sql;
	}
}
