<?php namespace Exolnet\Scaffolding\Controllers;

use App\Http\Controllers\Controller;
use Exolnet\Core\Arr;
use Exolnet\Scaffolding\Services\CrudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Engines\BaseEngine;

class CrudController extends Controller
{
	/**
	 * @var string
	 */
	protected $baseViewPath;

	/**
	 * @var \Exolnet\Scaffolding\Services\CrudService
	 */
	protected $crudService;

	/**
	 * @var array
	 */
	protected $labels = [];

	/**
	 * @var string
	 */
	protected $routeNamespace;

	/**
	 * @var bool
	 */
	protected $allowCreate = true;

	/**
	 * @var bool
	 */
	protected $allowEdit = true;

	/**
	 * @var bool
	 */
	protected $allowDestroy = true;

	/**
	 * @var array
	 */
	protected $validation = [];

	/**
	 * @var array
	 */
	private $baseLabels = [
		'name' => 'Items',
		'singular_name' => 'Item',
		'all_items' => 'All :name',
		'edit_item' => 'Edit :singular_name',
		'view_item' => 'View :singular_name',
		'update_item' => 'Update :singular_name',
		'add_item' => 'Add :singular_name',
		'new_item' => 'New :singular_name',
		'search_items' => 'Search :name',
		'filter_items' => 'Filter :name',
		'not_found' => 'No :singular_name_lower to display.',
		'notice_created' => 'The :singular_name_lower was successfully created.',
		'notice_updated' => 'The :singular_name_lower was successfully updated.',
		'notice_deleted' => 'The :singular_name_lower was successfully deleted.',
		'confirm_delete' => 'Are you sure you want to delete this :singular_name_lower?',
	];

	/**
	 * @return \Exolnet\Scaffolding\Services\CrudService
	 */
	public function getCrudService()
	{
		return $this->crudService;
	}

	/**
	 * @param \Exolnet\Scaffolding\Services\CrudService $crudService
	 * @return $this
	 */
	public function setCrudService(CrudService $crudService)
	{
		$this->crudService = $crudService;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getLabels()
	{
		$labels = $this->labels + $this->baseLabels;

		$replacements = [
			':name'                => $labels['name'],
			':singular_name'       => $labels['singular_name'],
			':name_lower'          => array_get($labels, 'name_lower', mb_strtolower($labels['name'])),
			':singular_name_lower' => array_get($labels, 'singular_name_lower', mb_strtolower($labels['singular_name'])),
		];

		return array_map(function($label) use ($replacements) {
			return strtr($label, $replacements);
		}, $labels);
	}

	/**
	 * @param string $key
	 * @param string|null $default
	 * @return string
	 */
	public function getLabel($key, $default = null)
	{
		return array_get($this->getLabels(), $key, $default);
	}

	/**
	 * @param string $action
	 * @param array $parameters
	 * @return string
	 */
	public function getRoute($action, $parameters = [])
	{
		return route($this->routeNamespace .'.'. $action, $parameters);
	}

	/**
	 * @return bool
	 */
	public function canCreate()
	{
		return $this->allowCreate;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $item
	 * @return bool
	 */
	public function canEdit(Model $item)
	{
		return $this->allowEdit;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $item
	 * @return bool
	 */
	public function canDestroy(Model $item)
	{
		return $this->allowDestroy;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		if ($request->get('draw')) {
			return $this->indexHandleDataTable();
		}

		$labels = $this->getLabels();

		return view($this->baseViewPath .'.index', [
			'labels' => $labels,
			'title'  => $labels['name'],
		]);
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function indexHandleDataTable()
	{
		$users = $this->crudService->getBaseDataTableData();

		/** @var \Yajra\Datatables\Engines\BaseEngine $dataTable */
		$dataTable = Datatables::of($users);

		$dataTable->addColumn('actions', function(Model $item) {
			return $this->getActions($item);
		});

		$this->transformDataTable($dataTable);

		return $dataTable->make(true);
	}

	/**
	 * @param \Yajra\Datatables\Engines\BaseEngine $dataTable
	 */
	protected function transformDataTable(BaseEngine $dataTable)
	{
		// Default behaviour - do nothing
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $item
	 * @return array
	 */
	protected function buildActions(Model $item)
	{
		$actions = [];

		if ($this->canEdit($item)) {
			$actions['edit'] = '<a href="'. $this->getRoute('edit', $item->getKey()) .'"><i class="fa fa-pencil"></i></a>';
		}

		if ($this->canDestroy($item)) {
			$actions['destroy'] = '<a href="'. $this->getRoute('destroy', $item->getKey()) .'" data-method="DELETE" data-ng-confirm="'. $this->getLabel('confirm_delete') .'"><i class="fa fa-trash"></i></a>';
		}

		return $actions;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $item
	 * @return string
	 */
	protected function getActions(Model $item)
	{
		return implode(' &nbsp; ', $this->buildActions($item));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function create()
	{
		$labels = $this->getLabels();

		return view($this->baseViewPath .'.create', [
			'labels'      => $labels,
			'title'       => $labels['new_item'],
			'urlBack'     => $this->getRoute('index'),
			'actionRoute' => $this->getRoute('store'),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \App\Http\Requests\UserRequest
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, $this->getRulesStore(), $this->getValidationMessages(), $this->getValidationAttributes());

		$data = Arr::mapNullOnEmpty($request->all());

		/** @var \Illuminate\Database\Eloquent\Model $item */
		$item = $this->getCrudService()->create($data);

		return redirect($this->getRoute('edit', $item->getKey()))
			->with('notice_success', $this->getLabel('notice_created'));
	}

	/**
	 * Show the specified resource.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function show(Model $model)
	{
		$labels = $this->getLabels();

		return view($this->baseViewPath .'.show', [
			'labels' => $labels,
			'model'  => $model,
			'title'  => $labels['view_item'],
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function edit(Model $model)
	{
		$labels = $this->getLabels();

		if (method_exists($model, 'translationsAsObject')) {
			$model->translation = $model->translationsAsObject();
		}

		return view($this->baseViewPath .'.edit', [
			'labels'      => $labels,
			'model'       => $model,
			'title'       => $labels['edit_item'],
			'urlBack'     => $this->getRoute('index'),
			'actionRoute' => $this->getRoute('update', $model->getKey()),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Illuminate\Database\Eloquent\Model $item
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Model $item)
	{
		$this->validate($request, $this->getRulesUpdate($item), $this->getValidationMessages(), $this->getValidationAttributes());

		$data = Arr::mapNullOnEmpty($request->all());
		$this->getCrudService()->update($item, $data);

		return redirect($this->getRoute('edit', $item->getKey()))
			->with('notice_success', $this->getLabel('notice_updated'));
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Model $model)
	{
		$this->getCrudService()->delete($model);

		return redirect($this->getRoute('index'))
			->with('notice_success', $this->getLabel('notice_deleted'));
	}

	/**
	 * @return array
	 */
	protected function getRules()
	{
		return [];
	}

	/**
	 * @return array
	 */
	protected function getRulesStore()
	{
		return $this->getRules();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return array
	 */
	protected function getRulesUpdate(Model $model)
	{
		return $this->getRules();
	}

	/**
	 * @return array
	 */
	protected function getValidationMessages()
	{
		return [];
	}

	/**
	 * @return array
	 */
	protected function getValidationAttributes()
	{
		return [];
	}
}
