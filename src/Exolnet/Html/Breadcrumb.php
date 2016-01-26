<?php

namespace Exolnet\Html;

use Countable;
use Exception;
use View;

class Breadcrumb implements Countable
{
	/**
	 * @var array
	 */
	protected $items = [];

	/**
	 * @var string
	 */
	protected $viewName = 'layouts.breadcrumb';

	/**
	 * Count links in the breadcrumb.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Append a link to the breadcrumb.
	 *
	 * @param string $label
	 * @param string $url
	 * @param array  $attributes
	 * @return $this
	 */
	public function push($label, $url = null, array $attributes = [])
	{
		array_push($this->items, [
			'label'      => $label,
			'url'        => $url,
			'attributes' => $attributes,
		]);

		return $this;
	}

	/**
	 * Pop a link off the end of the breadcrumb.
	 *
	 * @return array
	 */
	public function pop()
	{
		return array_pop($this->items);
	}

	/**
	 * Prepend a link to the breadcrumb.
	 *
	 * @param string $label
	 * @param string $url
	 * @param array  $attributes
	 * @return $this
	 */
	public function unshift($label, $url = null, array $attributes = [])
	{
		array_unshift($this->items, [
			'label'      => $label,
			'url'        => $url,
			'attributes' => $attributes,
		]);

		return $this;
	}

	/**
	 * Shift an link off the beginning of the breadcrumb.
	 *
	 * @return array
	 */
	public function shift()
	{
		return array_shift($this->items);
	}

	/**
	 * Reset link stored by the breadcrumb.
	 *
	 * @return $this
	 */
	public function reset()
	{
		$this->items = [];

		return $this;
	}

	/**
	 * Get the items for the breadcrumb.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Get the view name used to generate the breadcrumb.
	 *
	 * @return string
	 */
	public function getViewName()
	{
		return $this->viewName;
	}

	/**
	 * Set the view name used to generate the breadcrumb.
	 *
	 * @return $this
	 */
	public function setViewName($viewName)
	{
		$this->viewName = $viewName;

		return $this;
	}

	/**
	 * Create and return the view for the breadcrumb.
	 *
	 * @return \Illuminate\View\View
	 */
	public function getView()
	{
		return View::make($this->viewName, [
			'items' => $this->items,
		]);
	}

	/**
	 * Render the breadcrumb.
	 *
	 * @return string
	 */
	public function render()
	{
		return $this->getView()->render();
	}

	/**
	 * Get the object as a string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try {
			return $this->render();
		} catch (Exception $e) {
			return 'Unable to render breadcrumb: ' . $e->getMessage();
		}
	}

	/**
	 * Create a new breadcrumb instance.
	 *
	 * @return static
	 */
	public static function make()
	{
		return new static;
	}
}
