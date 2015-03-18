<?php

namespace Exolnet\Database\Eloquent\Relations;

use Closure;
use DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class Helper
{
	/**
	 * @param \Illuminate\Database\Eloquent\Relations\HasMany $relation
	 * @param array                                           $items
	 * @param \Closure|null                                   $onSave
	 */
	public static function syncHasMany(HasMany $relation, array $items, Closure $onSave = null)
	{
		DB::transaction(function () use ($relation, $items, $onSave) {
			self::syncHasManyInternal($relation, $items, $onSave);
		});
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Relations\HasMany $relation
	 * @param array                                           $items
	 * @param \Closure|null                                   $onSave
	 */
	protected static function syncHasManyInternal(HasMany $relation, array $items, Closure $onSave = null)
	{
		$newItems = [];
		$related = $relation->getRelated();
		$keyName = $related->getKeyName();

		// 1. Extract all updated keys
		$updatedKeys = Arr::pluck($items, $keyName);

		// 2. Delete old relations
		static::deleteAllRelatedExcept($relation, $updatedKeys);

		// 3. Update existing relations
		$models = $relation->all();
		foreach ($items as $item) {
			$model = $models->find(array_get($item, $keyName));

			if ($model === null) {
				$newItems[] = $item;
				continue;
			}

			// Update relation data
			$model->fill($item)->save();

			if ($onSave) {
				$onSave($model, $item);
			}
		}

		// 4. Insert new relations
		foreach ($newItems as $item) {
			$model = $relation->create($item);

			if ($onSave) {
				$onSave($model, $item);
			}
		}
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Relations\Relation $relation
	 * @param array                                            $excludedIds
	 */
	public static function deleteAllRelatedExcept(Relation $relation, $excludedIds = [])
	{
		$related = $relation->getRelated();
		$keyName = $related->getKeyName();
		$query = $relation->getQuery();

		if (count($excludedIds) > 0) {
			$query->whereNotIn($keyName, $excludedIds);
		}

		$query->delete();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Relations\BelongsTo $belongsTo
	 * @param mixed|null                                        $object
	 * @return mixed
	 */
	public static function setBelongsTo(BelongsTo $belongsTo, $object = null)
	{
		return $object ? $belongsTo->associate($object) : $belongsTo->dissociate();
	}
}
