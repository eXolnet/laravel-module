<?php namespace Exolnet\Database\Eloquent\Relations;
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

use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Helper
{
	/**
	 * @param HasMany  $relation
	 * @param array    $items
	 * @param callable $onSave
	 */
	public static function syncHasMany(HasMany $relation, array $items, Closure $onSave = null)
	{
		\DB::transaction(function() use ($relation, $items, $onSave) {
			self::syncHasManyInternal($relation, $items, $onSave);
		});
	}

	protected static function syncHasManyInternal(HasMany $relation, array $items, Closure $onSave = null)
	{
		$updated_keys = [];
		$new_items    = [];

		// 1. Update existing relations and prepare the delete and create steps
		foreach ($items as $item) {
			$model = static::getRelatedModel($relation, $item);

			if ($model === null) {
				$new_items[] = $item;
				continue;
			}

			// On met à jour la relation
			$model->fill($item)->save();

			if ($onSave) {
				$onSave($model, $item);
			}

			$updated_keys[] = $model->getKey();
		}

		// 2. Delete old relations
		static::deleteAllRelatedExcept($relation, $updated_keys);

		// 3. Create new relations
		foreach ($new_items as $item) {
			$model = $relation->create($item);

			if ($onSave) {
				$onSave($model, $item);
			}
		}
	}

	/**
	 * @param Relation $relation
	 * @param array    $item
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static
	 */
	protected static function getRelatedModel(Relation $relation, array $item)
	{
		$related  = $relation->getRelated();
		$key_name = $related->getKeyName();
		$key      = array_get($item, $key_name, null);

		if ($key === null) {
			return null;
		}

		return $related->find($key);
	}

	/**
	 * @param Relation $relation
	 * @param array    $excluded_ids
	 */
	protected static function deleteAllRelatedExcept(Relation $relation, $excluded_ids = [])
	{
		$related  = $relation->getRelated();
		$key_name = $related->getKeyName();
		$query    = $relation->getQuery();

		if (count($excluded_ids) > 0) {
			$query->whereNotIn($key_name, $excluded_ids);
		}

		$query->delete();
	}
}
