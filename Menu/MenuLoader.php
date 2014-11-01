<?php namespace Exolnet\Menu;

use Config;
use Illuminate\Support\Str;
use Lang;
use Menu\Items\ItemList;
use Menu\Menu;
use URL;

class MenuLoader
{
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

			// Create children container
			$items = Menu::items($menu_name);

			$content = '';
			$icon_content = '';
			if ($icon) {
				$icon_content .= '<span class="'.$icon.'"></span> ';
			}

			$content .= $icon_content.$label;

			if ($uri) {
				$item = $parent->add($uri, $content, $items);

				if ($uri !== '#') {
					$item->activePattern('^'. preg_quote($uri, '/').'(.+)$');
				}
			} else {
				$parent->raw($content, $items);
			}

			if ($children) {
				$this->buildMenu($children, $items);
			}
		}
	}
}
