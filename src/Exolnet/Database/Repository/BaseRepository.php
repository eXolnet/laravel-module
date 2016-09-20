<?php

namespace Exolnet\Database\Repository;

use Exolnet\Core\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

abstract class BaseRepository implements Repository {
	/**
	 * @return string
	 */
	public abstract function getModelClass();

	/**
	 * Save a new model and return the instance.
	 *
	 * @param  array  $attributes
	 * @return static
	 */
	public function create(array $attributes)
	{
		return $this->getModel()->create($attributes);
	}

	/**
	 * Get all of the models from the database.
	 *
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function all($columns = array('*'))
	{
		return $this->getModel()->all($columns);
	}

	public function find($id, $columns = array('*'))
	{
		return $this->getModel()->find($id, $columns);
	}

	/**
	 * Being querying a model with eager loading.
	 *
	 * @param  array|string  $relations
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function with($relations)
	{
		return $this->getModel()->with($relations);
	}

	/**
	 * Destroy the models for the given IDs.
	 *
	 * @param  array|int  $ids
	 * @return int
	 */
	public function destroy($ids)
	{
		return $this->getModel()->destroy($ids);
	}

	/**
	 * Update the model in the database.
	 *
	 * @param  array  $attributes
	 * @return bool|int
	 */
	public function update(array $attributes = array())
	{
		return $this->getModel()->update($attributes);
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		return $this->getModel()->save($options);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Model
	 * @throws \Exolnet\Core\Exceptions\RepositoryException
	 */
	protected function getModel()
	{
		$modelClass = $this->getModelClass();

		$model = App::make($modelClass);

		if ( ! $model instanceof Model) {
			throw new RepositoryException('Class '.$modelClass.' must be an instance of Illuminate\\Database\\Eloquent\\Model.');
		}

		return $model;
	}

	/**
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 * @throws \Exolnet\Core\Exceptions\RepositoryException
	 */
	public function __call($method, $args)
	{
		$model = $this->getModel();

		switch (count($args))
		{
			case 0:
				return $model->$method();

			case 1:
				return $model->$method($args[0]);

			case 2:
				return $model->$method($args[0], $args[1]);

			case 3:
				return $model->$method($args[0], $args[1], $args[2]);

			case 4:
				return $model->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array([$model, $method], $args);
		}
	}
}
