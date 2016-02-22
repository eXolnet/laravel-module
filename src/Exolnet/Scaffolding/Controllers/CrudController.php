<?php namespace Exolnet\Scaffolding\Controllers;

use Exolnet\Scaffolding\Services\CrudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
		'not_found' => 'No item to display.',
		'notice_created' => 'The :sungular_name was successfully created.',
		'notice_updated' => 'The :sungular_name was successfully updated.',
		'notice_deleted' => 'The :sungular_name was successfully deleted.',
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
			':name'          => $labels['name'],
			':singular_name' => $labels['singular_name'],
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

		$dataTable->addColumn('actions', function($item) {
			$actions = [
				'edit'   => '<a href="#"><i class="fa fa-pencil"></i></a>',
				'delete' => '<a href="#"><i class="fa fa-trash"></i></a>',
			];

			return implode(' &nbsp; ', $actions);
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
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$labels = $this->getLabels();

		return view($this->baseViewPath .'.create', [
			'labels' => $labels,
			'title'  => $labels['new_item'],
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
		$this->getCrudService()->create($request->all());

		return redirect()
			->route('admin.user.index')
			->with('notice_success', $this->getLabel('notice_created'));
	}

	/**
	 * Show the specified resource.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Http\Response
	 */
	public function show(Model $model)
	{
		$labels = $this->getLabels();

		return view('admin.user.show', [
			'labels' => $labels,
			'model'  => $model,
			'title'  => $labels['view_item'],
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Model $model)
	{
		$labels = $this->getLabels();

		return view('admin.user.edit', [
			'labels' => $labels,
			'model'  => $model,
			'title'  => $labels['edit_item'],
		]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Model $model)
	{
		$this->getCrudService()->update($model, $request->all());

		return redirect()
			->route('admin.user.edit', $model->getKey)
			->with('message', $this->getLabel('notice_updated'));
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy(Model $model)
	{
		$this->getCrudService()->delete($model);

		return redirect()
			->route('admin.user.index')
			->with('message', $this->getLabel('notice_deleted'));
	}
}
