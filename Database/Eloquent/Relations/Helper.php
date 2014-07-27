<?php namespace Exolnet\Database\Eloquent\Relations;
/**
 * @copyright  Copyright (c) 2014 eXolnet
 * @author     eXolnet Inc. <info@exolnet.com>
 * @package    Exolnet
 * @subpackage Database
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
