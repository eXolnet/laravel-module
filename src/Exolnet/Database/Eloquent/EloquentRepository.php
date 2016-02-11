<?php namespace Exolnet\Database\Eloquent;

use Exolnet\Core\Exceptions\RepositoryException;
use Exolnet\Database\Repository\Repository;
use Illuminate\Database\Eloquent\Model;

class EloquentRepository implements Repository
{
	/**
	 * @var string
	 */
	protected $model;

	/**
	 * @return \Illuminate\Database\Eloquent\Model
	 * @throws \Exolnet\Core\Exceptions\RepositoryException
	 */
	public function newModel()
	{
		$model = new $this->model();

		if ( ! $model instanceof Model) {
			throw new RepositoryException('Class '. $this->model .' must be an instance of Illuminate\\Database\\Eloquent\\Model.');
		}

		return $model;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function newQuery()
	{
		return $this->newModel()->newQuery();
	}

	/**
	 * @param array $columns
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function all(array $columns = ['*'])
	{
		return $this->newQuery()->get($columns);
	}

	/**
	 * @param mixed $id
	 * @param array $columns
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
	 */
	public function find($id, array $columns = ['*'])
	{
		return $this->newQuery()->find($id, $columns);
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return static
	 */
	public function create(Model $model)
	{
		return $model->create();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return bool|int
	 */
	public function update(Model $model)
	{
		return $model->update();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return bool
	 */
	public function save(Model $model)
	{
		return $model->save();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return bool|null
	 * @throws \Exception
	 */
	public function delete(Model $model)
	{
		return $model->delete();
	}
}
