<?php namespace Exolnet\Console\Commands;

use App;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Schema;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FillModel extends Command {
	/**
	 * @var string
	 */
	protected $name = 'fill:models';

	/**
	 * @var string
	 */
	protected $description = "Parse table to fill a model with it's relations, getters and setters.";

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * @var array
	 */
	protected $defaultTypes = [
		'bool',
		'double',
		'float',
		'int',
		'string',
		'mixed',
	];

	/**
	 * @var array
	 */
	protected $availableModels = [];

	/**
	 * @param  \Illuminate\Filesystem\Filesystem $files
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
		$finder = $this->buildModuleFinder()
			->contains('protected $table');

		foreach ($finder as $file) {
			$this->availableModels[] = $this->getQualifiedName($file);
		}

		foreach ($finder as $file) {
			$this->fillModel($file);
		}
	}

	public function fillModel(SplFileInfo $fileInfo)
	{
		$qualifiedName = $this->getQualifiedName($fileInfo);
		$qualifiedNamespace = $this->getNamespace($qualifiedName);

		$this->info($qualifiedName);

		if ( ! $this->confirm('Would you like to fill this model? [Yn] ', true)) {
			return;
		}

		/** @var \Illuminate\Database\Eloquent\Model $model */
		$model = App::make($qualifiedName);
		$content = $this->files->get($fileInfo);
		$table = $model->getTable();
		$keyName = $model->getKeyName();

		preg_match_all('/use (.+?);/', $content, $usesMatched);
		$uses = $usesMatched[1];

		$columns = Schema::getColumnListing($table);
		$changeCode = [];
		$changeUses = [];

		foreach ($columns as $column) {
			$this->info('    - ' . $column);

			if ($column === $keyName) {
				// Primary key
				$this->comment('        - Primary Key');
			} elseif (Str::endsWith($column, '_id')) {
				// Foreign key
				$this->comment('        - Foreign Key');

				$related = $this->askWithCompletion('What is the related BelongsTo model (leave empty if not applicable)? ', $this->availableModels);

				if ( ! $related) {
					continue;
				}

				$related        = '\\'. ltrim($related, '\\');
				$relationColumn = preg_replace('/_id$/i', '', $column);

				$changeUses[] = $related;
				$changeCode = array_merge($this->makeRelation($column, $related), $changeCode);
				$changeCode += $this->makeRelationGetterSetter($relationColumn, $related);
			} elseif (Str::startsWith($column, ['is_', 'has_']) || Str::endsWith($column, 'ed')) {
				// Boolean column
				$this->comment('        - Boolean');

				$changeCode += $this->makeGetterSetter($column, 'is', null, 'bool');
			} elseif (Str::endsWith($column, ['_on', '_at', 'date'])) {
				// Date column
				$this->comment('        - Date');

				$changeUses[] = '\\DateTime';
				$changeCode += $this->makeGetterSetter($column, 'get', 'DateTime');
			} else {
				// Normal Getter/Setter
				$this->comment('        - Normal');

				$changeCode += $this->makeGetterSetter($column);
			}
		}

		// Apply changes
		if ( ! $this->confirm('Apply changes? [yN]')) {
			return;
		}

		// Append uses
		$content = preg_replace('/(use (.+?);)(.*use (.+?);)?/s', '/* PHP-USES */', $content);

		$uses = array_merge($changeUses, $uses);
		$uses = array_unique($uses);
		sort($uses);

		$uses = array_filter($uses, function ($use) use ($qualifiedNamespace) {
			return $qualifiedNamespace !== $this->getNamespace($use);
		});

		$uses = implode(PHP_EOL, array_map(function ($use) {
			return 'use \\' . ltrim($use, '\\') . ';';
		}, $uses));
		$content = str_replace('/* PHP-USES */', $uses, $content);

		// Append functions
		foreach ($changeCode as $functionName => $code) {
			if (method_exists($model, $functionName)) {
				unset($changeCode[$functionName]);
			}
		}

		if (count($changeCode) > 0) {
			$changeCode = PHP_EOL . implode(PHP_EOL . PHP_EOL, $changeCode) . PHP_EOL;

			$lastBracket = strrpos($content, '}');
			$content = substr_replace($content, $changeCode, $lastBracket, 0);
		}

		// Update model
		$this->files->put($fileInfo, $content);
	}

	public function makeGetterSetter($column, $getterPrefix = 'get', $typeHinting = null, $cast = null)
	{
		$name = Str::studly($column);
		$name = preg_replace('/^(is_|has_)/i', '', $name);
		$camelColumn = Str::camel($column);
		$typeComment = $typeHinting ?: $cast;

		if ( ! $typeComment) {
			$typeComment = $this->askWithCompletion('Which type (default string)? ', $this->defaultTypes, 'string');
		}

		$nameGetter = $getterPrefix . $name;
		$nameSetter = 'set' . $name;

		return [
			// **** GETTER ****
			$nameGetter => '	/**
	 * @return ' . $typeComment . '
	 */
	public function ' . $nameGetter . '()
	{
		return ' . ($cast ? '(' . $cast . ')' : '') . '$this->' . $column . ';
	}',

			// **** SETTER ****
			$nameSetter => '	/**
	 * @param ' . $typeComment . ' $' . $camelColumn . '
	 * @return $this
	 */
	public function set' . $name . '(' . ($typeHinting ? $typeHinting . ' ' : '') . '$' . $camelColumn . ')
	{
		$this->' . $column . ' = ' . ($cast ? '(' . $cast . ')' : '') . '$' . $camelColumn . ';

		return $this;
	}',
		];
	}

	public function makeRelation($column, $related)
	{
		$functionName = Str::camel($column);
		$functionName = preg_replace('/Id$/i', '', $functionName);

		return [
			$functionName => '	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function ' . $functionName . '()
	{
		return $this->belongsTo(' . $related . '::class);
	}',
		];
	}

	public function makeRelationGetterSetter($column, $related)
	{
		$name = Str::studly($column);
		$camelColumn = Str::camel($column);
		$typeHinting = preg_replace('#(.*)\\\\#', '', $related);
		$typeComment = $related;

		$nameGetter = 'get' . $name;
		$nameSetter = 'set' . $name;

		return [
			// **** GETTER ****
			$nameGetter => '	/**
	 * @return ' . $typeComment . '
	 */
	public function ' . $nameGetter . '()
	{
		return $this->' . $column . ';
	}',

			// **** SETTER ****
			$nameSetter => '	/**
	 * @param ' . $typeComment . ' $' . $camelColumn . '
	 * @return $this
	 */
	public function set' . $name . '(' . ($typeHinting ? $typeHinting . ' ' : '') . '$' . $camelColumn . ')
	{
		$this->' . $camelColumn . '()->associate($' . $camelColumn . ');

		return $this;
	}',
		];
	}

	/**
	 * @param \Symfony\Component\Finder\SplFileInfo $fileInfo
	 * @return string
	 */
	protected function getQualifiedName(SplFileInfo $fileInfo)
	{
		if ( ! preg_match('/namespace (.+?);/', $fileInfo->getContents(), $match)) {
			return null;
		}

		return $match[1].'\\'. $fileInfo->getBasename('.php');
	}

	private function getNamespace($qualifiedName)
	{
		if (strpos($qualifiedName, '\\') === false) {
			return '\\';
		}

		return preg_replace('#^(.*)\\\\.+?$#', '\1', $qualifiedName);
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
			->notPath('Exolnet/Console/Commands')
			->name('/\.php$/');

		/** @var string $directory */
		foreach ($this->getModuleDirectories() as $directory) {
			$finder->in($directory);
		}

		return $finder;
	}
}
