<?php namespace Exolnet\Menu;

use Config;
use Lang;
use Menu\Menu;
use URL;

class MenuLoader
{
	public function fromConfig($name, $menu_file)
	{
		$data = Config::get($menu_file);
		return $this->fromArray($name, $data);
	}

	public function fromArray($name, array $menu)
	{
		$parent = Menu::handler($name);
		$this->buildMenu($menu, $parent);
		return $parent;
	}

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

			if ($uri) {
				$content .= '<a href="'.URL::to($uri).'">'.$icon_content.$label.'</a>';
			} else {
				$content .= $icon_content.$label;
			}

			$parent->raw($content, $items);

			if ($children) {
				$this->buildMenu($children, $items);
			}
		}
	}
}