<?php namespace Exolnet\Database\Eloquent;

use Illuminate\Support\Facades\DB;

trait OrderableTrait
{
	/**
	 * @return void
	 */
	public static function bootOrderableTrait()
	{
		static::creating(function ($model) {
			if ( ! $model->isOrderable()) {
				return;
			}

			$order = $model->getOrder();
			$orderMax = $model->getOrderMax();

			if ($orderMax === null) {
				$orderMax = -1;
			}

			// Limit order
			$order = $order ? max(0, min($orderMax + 1, $order)) : $orderMax + 1;

			$model->setOrder($order);

			if ($orderMax >= $order) {
				$model->incrementOrders($order, $orderMax);
			}
		});

		static::updating(function ($model) {
			if ( ! $model->isOrderable()) {
				return;
			}

			// Limit order
			$orderMax = $model->getOrderMax();
			$newOrder = max(0, min($orderMax, $model->getOrder()));
			$model->setOrder($newOrder);

			// Update siblings orders
			$order = $model->getOriginal($model->getOrderColumn());

			if ($newOrder > $order) {
				$model->decrementOrders($order, $newOrder);
			} elseif ($newOrder < $order) {
				$model->incrementOrders($order, $newOrder);
			}
		});

		static::deleting(function ($model) {
			if ( ! $model->isOrderable()) {
				return;
			}

			$orderMax = $model->getOrderMax();
			$order = $model->getOriginalOrder();

			$model->decrementOrders($order, $orderMax);
		});
	}

	/**
	 * @return bool
	 */
	public function isOrderable()
	{
		return isset($this->orderable) ? $this->orderable : true;
	}

	/**
	 * @return string
	 */
	public function getOrderColumn()
	{
		return isset($this->orderColumn) ? $this->orderColumn : 'order';
	}

	/**
	 * @return mixed
	 */
	public function getOriginalOrder()
	{
		return $this->getOriginal($this->getOrderColumn());
	}

	/**
	 * @return int|null
	 */
	public function getOrder()
	{
		return $this->getAttribute($this->getOrderColumn());
	}

	/**
	 * @param int|null $order
	 * @return $this
	 */
	protected function setOrder($order)
	{
		$this->setAttribute($this->getOrderColumn(), $order);

		return $this;
	}

	public function getOrderMax()
	{
		return $this->newOrderQuery()->max($this->getOrderColumn());
	}

	/**
	 * @return array
	 */
	public function getUniqueColumns()
	{
		return (array)(isset($this->orderUniqueColumns) ? $this->orderUniqueColumns : []);
	}

	/**
	 * @return bool
	 */
	public function isFirst()
	{
		return $this->getOrder() === 0;
	}

	/**
	 * @return bool
	 */
	public function isLast()
	{
		return $this->getOrder() === $this->getOrderMax();
	}

	/**
	 * @return bool
	 */
	public function hasUniqueColumn()
	{
		return $this->getUniqueColumn() !== null;
	}

	/**
	 * @return $this|\Exolnet\Database\Eloquent\OrderableTrait|void
	 */
	public function moveFirst()
	{
		return $this->moveTo(0);
	}

	/**
	 * @return $this|\Exolnet\Database\Eloquent\OrderableTrait|void
	 */
	public function moveUp()
	{
		return $this->moveTo($this->getOrder() - 1);
	}

	/**
	 * @return $this|\Exolnet\Database\Eloquent\OrderableTrait|void
	 */
	public function moveDown()
	{
		return $this->moveTo($this->getOrder() + 1);
	}

	/**
	 * @return $this|\Exolnet\Database\Eloquent\OrderableTrait|void
	 */
	public function moveLast()
	{
		return $this->moveTo($this->getOrderMax());
	}

	/**
	 * @param $newOrder
	 * @return $this|void
	 */
	public function moveTo($newOrder)
	{
		if ( ! $this->isOrderable()) {
			return $this;
		}

		if ( ! $this->exists) {
			throw new \LogicException('Uncreated models could not be moved.');
		} elseif ($this->isDirty()) {
			throw new \LogicException('Dirty models could not be moved.');
		}

		$order = $this->getOriginalOrder();
		$newOrder = max(min($newOrder, $this->getOrderMax()), 0);

		if ($newOrder === $order) {
			return;
		}

		DB::transaction(function () use ($newOrder) {
			$this->setOrder($newOrder)->save();
		});

		return $this;
	}

	/**
	 * @param string $direction
	 * @param int    $from
	 * @param int    $to
	 * @return void
	 */
	private function updateOrders($direction, $from, $to)
	{
		if ( ! $this->isOrderable()) {
			return;
		}

		$query = DB::table($this->getTable());

		$this->applyUniqueColumn($query);

		$query->whereBetween($this->getOrderColumn(), [
			min($from, $to),
			max($from, $to),
		])
			->where($this->getKeyName(), '<>', $this->getKey())
			->$direction($this->getOrderColumn());
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @return void
	 */
	private function incrementOrders($from, $to)
	{
		$this->updateOrders('increment', $from, $to);
	}

	/**
	 * @param int $from
	 * @param int $to
	 * @return void
	 */
	private function decrementOrders($from, $to)
	{
		$this->updateOrders('decrement', $from, $to);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function newOrderQuery()
	{
		/** @var \Illuminate\Database\Eloquent\Builder $query */
		$query = $this->newQuery();

		$this->applyUniqueColumn($query);

		return $query;
	}

	/**
	 * @param $query
	 */
	protected function applyUniqueColumn($query)
	{
		foreach ($this->getUniqueColumns() as $column) {
			$value = $this->getAttribute($column);
			$query->where($column, '=', $value);
		}
	}
}
