<?php namespace Exolnet\Database\Eloquent;

trait NoUpdatedAtTrait
{
	/**
	 * @param $value
	 */
	public function setUpdatedAtAttribute($value)
	{
		// Do nothing.
	}
}
