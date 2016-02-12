<?php namespace Exolnet\Http\Middleware;

use Closure;
use Exolnet\Routing\LocaleService;
use Exolnet\Routing\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleSetter {
	/**
	 * @var \Exolnet\Routing\LocaleService
	 */
	private $localeService;

	/**
	 * LocaleSetter constructor.
	 *
	 * @param \Exolnet\Routing\LocaleService $localeService
	 */
	public function __construct(LocaleService $localeService)
	{
		$this->localeService = $localeService;
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$locale = $this->localeService->extractLocale($request);

		App::setLocale($locale);

		// TODO-TR: Support something else than _CA.utf8 <trochette@exolnet.com>
		setlocale(LC_COLLATE, $locale . '_CA.utf8');
		setlocale(LC_CTYPE, $locale . '_CA.utf8');
		setlocale(LC_TIME, $locale . '_CA.utf8');

		return $next($request);
	}


}
