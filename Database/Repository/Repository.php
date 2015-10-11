<?php

namespace Exolnet\Database\Repository;

interface Repository {
	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array $attributes
	 * @return static
	 */
	public function create(array $attributes);

	/**
	 * Get all of the models from the database.
	 *
	 * @param  array $columns
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($columns = ['*']);

	public function find($id, $columns = ['*']);

	/**
	 * Being querying a model with eager loading.
	 *
	 * @param  array|string $relations
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function with($relations);

	/**
	 * Destroy the models for the given IDs.
	 *
	 * @param  array|int $ids
	 * @return int
	 */
	public function destroy($ids);

	/**
	 * Update the model in the database.
	 *
	 * @param  array $attributes
	 * @return bool|int
	 */
	public function update(array $attributes = []);

	/**
	 * Save the model to the database.
	 *
	 * @param  array $options
	 * @return bool
	 */
	public function save(array $options = []);
}
