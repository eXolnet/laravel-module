<?php namespace Exolnet\Menu;

use Config;
use Lang;
use Menu\Menu;

class MenuLoader {
	/**
	 * @var array
	 */
	protected $callbacks = [];

	/**
	 * @param $name
	 * @param $menu_file
	 * @return mixed
	 */
	public function fromConfig($name, $menu_file)
	{
		$data = Config::get($menu_file);
		return $this->fromArray($name, $data);
	}

	/**
	 * @param       $name
	 * @param array $menu
	 * @return mixed
	 */
	public function fromArray($name, array $menu)
	{
		$parent = Menu::handler($name);
		$this->buildMenu($menu, $parent);

		return $parent;
	}

	/**
	 * @param array $menu
	 * @param       $parent
	 */
	protected function buildMenu(array $menu, $parent)
	{
		foreach ($menu as $menu_name => $menu_details) {
			$uri = array_get($menu_details, 'uri', null);
			$label = array_get($menu_details, 'label', null);
			$label = $label ? Lang::get($label) : null;
			$icon = array_get($menu_details, 'icon', null);
			$children = array_get($menu_details, 'children', false);

			$skipItem = false;
			foreach ($this->callbacks as $callback) {
				$result = $callback($menu_details);
				if ( ! $result) {
					$skipItem = true;
					break;
				}
			}

			if ($skipItem) {
				continue;
			}

			// Create children container
			$items = Menu::items($menu_name);

			$content = '';
			$icon_content = '';
			if ($icon) {
				$icon_content .= '<span class="' . $icon . '"></span> ';
			}

			$content .= $icon_content . $label;

			if ($uri) {
				$item = $parent->add($uri, $content, $items);

				if ($uri !== '#') {
					$item->activePattern('^\/' . preg_quote($uri, '/') . '(.+)$');
				}
			} else {
				$parent->raw($content, $items);
			}

			if ($children) {
				$this->buildMenu($children, $items);
			}
		}
	}

	/**
	 * @param callable $callback
	 */
	public function setMenuItemCallback(callable $callback)
	{
		$this->callbacks[] = $callback;
	}
}
