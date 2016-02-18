<?php namespace Exolnet\Scaffolding\Services;

use Illuminate\Database\Eloquent\Model;

interface CrudService
{
	/**
	 * @return \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
	 */
	public function getBaseDataTableData();

	/**
	 * @param array $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function create(array $data);

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param array $data
	 * @return void
	 */
	public function update(Model $model, array $data);

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return bool
	 */
	public function delete(Model $model);
}
