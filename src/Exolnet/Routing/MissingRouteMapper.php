<?php namespace Exolnet\Routing;

use Illuminate\Http\Request;

class MissingRouteMapper {
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return string
	 */
	protected function getCurrentPath(Request $request)
	{
		return trim($request->path(), '/');
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse|null
	 */
	public function map(Request $request)
	{
		return $this->mapStatic($request) ?: $this->mapRegex($request);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse|null
	 */
	public function mapStatic(Request $request)
	{
		$path     = $this->getCurrentPath($request);
		$mappings = (array)config('missing-route-mapping.static', []);

		if (array_key_exists($path, $mappings)) {
			return redirect()->to($mappings[$path]);
		}

		return null;
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\RedirectResponse|null
	 */
	public function mapRegex(Request $request)
	{
		$path     = $this->getCurrentPath($request);
		$mappings = (array)config('missing-route-mapping.regex', []);

		foreach ($mappings as $mapping => $to) {
			if (preg_match('#'. $mapping .'#i', $path)) {
				return redirect()->to($to, 301);
			}
		}

		return null;
	}
}
