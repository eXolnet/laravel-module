<?php namespace Exolnet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Finder\Finder;

class MakeModels extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:models {--fillModels : Fill models with getters and setters} {--disable-timestamps} {--namespace=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create models from database schema';

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * @var string
	 */
	protected $namespaces;

	/**
	 * @var array
	 */
	protected $nonFillableColumns = [
		'is_active', 'created_at', 'updated_at', 'deleted_at', 'created_on', 'updated_on', 'deleted_on',
	];

	/**
	 * GenerateModels constructor.
	 *
	 * @param \Illuminate\Filesystem\Filesystem $files
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		// Get all tables inside the schema
		$tables = DB::select('SHOW TABLES');

		// Get a list of tables for which there is no model
		$tablesList = $this->retrieveMissingModels($tables);

		// Get a list of directories inside the project module
		$this->namespaces = $this->retrieveNamespaces();

		// Generate all models
		$this->createModelsFromATablesList($tablesList);

		if ($this->option('fillModels')) {
			$this->call('make:models:accessors');
		}
	}

	/**
	 * @param array $tables
	 * @return array
	 */
	private function retrieveMissingModels(array $tables)
	{
		$tables_in_database = 'Tables_in_' . Config::get('database.connections.mysql.database');
		$tablesList = [];

		foreach ($tables as $table) {
			$tableName = $table->$tables_in_database;

			if ($tableName !== 'migrations') {
				$finder = $this->buildModuleFinder()
					->contains('protected $table = \'' . $tableName . '\'');

				if ( ! count($finder)) {
					$tablesList[] = $tableName;
				}
			}
		}

		if ( ! count($tablesList)) {
			die('.');
		}

		return (array)$this->choice('Which table(s) do you want a model for?', $tablesList, null, null, true);
	}

	/**
	 * @return array
	 */
	private function retrieveNamespaces()
	{
		$finder = $this->buildModuleFinder()
			->contains('/namespace (.+?);/');

		$namespaces = [];

		$basePathLength = strlen(base_path());

		/** @var \Symfony\Component\Finder\SplFileInfo $file */
		foreach ($finder as $file) {
			if ( ! preg_match('/namespace (.+?);/', $file->getContents(), $match)) {
				throw new \RuntimeException('Unable to find the namespace in file.');
			}

			$namespace = ltrim($match[1], '\\');
			$namespaces[$namespace] = substr($file->getPath(), $basePathLength + 1);
		}

		return $namespaces;
	}

	/**
	 * @param array $tablesList
	 */
	private function createModelsFromATablesList(array $tablesList)
	{
		$progress = $this->output->createProgressBar(count($tablesList));

		foreach ($tablesList as $tableName) {
			$modelName = studly_case($tableName);
			$namespaces = array_keys($this->namespaces);

			$namespace = $this->option('namespace') ?: $this->anticipate('Which namespace do you want to use for ' . $modelName . '? [' . implode(', ', $namespaces) . ']', $namespaces);
			$columns = Schema::getColumnListing($tableName);

			if ( ! array_key_exists($namespace, $this->namespaces)) {
				$this->namespaces[$namespace] = trim($this->anticipate('What is this namespace directory?', array_values($this->namespaces)), '/');
			}

			$this->generateModel($namespace, $modelName, $tableName, $columns);

			$progress->advance();
		}

		$progress->finish();
		$this->line('');
	}

	/**
	 * @param string $namespace
	 * @param string $modelName
	 * @param string $tableName
	 * @param array|null $columns
	 */
	private function generateModel($namespace, $modelName, $tableName, array $columns = null)
	{
		$directory = $this->namespaces[$namespace];

		$primaryKey = ! in_array('id', $columns) ? $columns[0] : 'id';

		$nonFillableColumns = $this->nonFillableColumns;
		$nonFillableColumns[] = $primaryKey;

		$fillableColumns = array_diff($columns, $nonFillableColumns);

		$template = \View::file(__DIR__ .'/templates/model.blade.php', [
			'primaryKey'        => $primaryKey,
			'namespace'         => $namespace,
			'name'              => $modelName,
			'tableName'         => $tableName,
			'disableTimestamps' => $this->option('disable-timestamps'),
			'fillableColumns'   => $this->formatArray($fillableColumns),
		]);

		$path = base_path($directory);
		$fileName = $modelName . '.php';

		if ( ! $this->files->isDirectory($path)) {
			$this->files->makeDirectory($path);
		}

		$this->files->put($path . '/' . $fileName, $template);

		$this->comment(' ' . $fileName . ' has been successfully created inside ' . $path);
	}

	/**
	 * @param array $array
	 * @return string
	 */
	private function formatArray(array $array)
	{
		return implode(', ', array_map(function ($str) {
			return sprintf("'%s'", $str);
		}, $array));
	}

	/**
	 * @return array
	 */
	private function getModuleDirectories()
	{
		return [
			app_path(),
			base_path('src'),
			base_path('vendor/exolnet'),
		];
	}

	/**
	 * @return \Symfony\Component\Finder\Finder
	 */
	private function buildModuleFinder()
	{
		/** @var \Symfony\Component\Finder\Finder $finder */
		$finder = App::make(Finder::class)
			->files()
			->name('/\.php$/');

		/** @var string $directory */
		foreach ($this->getModuleDirectories() as $directory) {
			if ( ! $this->files->exists($directory)) {
				continue;
			}

			$finder->in($directory);
		}

		return $finder;
	}
}
