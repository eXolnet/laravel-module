<?php namespace Exolnet\Test;

use DB;
use Exception;
use Exolnet\Test\Traits\AssertionsTrait;
use Faker\Factory as FakerFactory;
use Mockery as m;

abstract class TestCaseBrowserKit extends \Laravel\BrowserKitTesting\TestCase {
	use AssertionsTrait;

	/**
	 * @var \Exolnet\Test\DatabaseMigratorFactory
	 */
	protected static $databaseMigrator;

	/**
	 * @var bool
	 */
	protected static $migrationFailed = false;

	/**
	 * @var \Faker\Generator
	 */
	protected $faker;

	/**
	 * @var bool
	 */
	protected static $forceBoot = false;

	/**
	 * @var bool
	 */
	protected static $environmentSetup = false;

	/**
	 * The base URL to use while testing the application.
	 *
	 * @var string
	 */
	protected $baseUrl = 'http://localhost';

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		// in src/Exolnet/src/Exolnet/Test or vendor/exolnet/laravel-module/src/Exolnet/Test
		$testedPaths = [
			__DIR__ . '/../../../../../bootstrap/app.php',
			__DIR__ . '/../../../../../../bootstrap/app.php',
		];

		$app = null;
		foreach ($testedPaths as $testedPath) {
			if (file_exists($testedPath)) {
				$app = require $testedPath;
				break;
			}
		}

		if ( ! $app) {
			throw new \RuntimeException('Could not find bootstrap/app.php');
		}

		$app->loadEnvironmentFrom('.env.testing');

		$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

		return $app;
	}

	/**
	 * @throws \Exception
	 */
	public function setUp()
	{
		parent::setUp();

		$this->faker = FakerFactory::create();

		if (self::$migrationFailed) {
			$this->markTestSkipped('Previous migration failed.');
			return;
		}

		try {
			$this->setupDatabaseMigrator();
			self::$databaseMigrator->run();
		} catch (Exception $e) {
			self::$migrationFailed = true;
			throw $e;
		}

		self::bootModels();
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		if ( ! self::$migrationFailed) {
			DB::disconnect();
			m::close();

			$this->tearDownModels();

			$this->app->reset();
			$this->app = null;
		}

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	protected function setupDatabaseMigrator()
	{
		if (self::$databaseMigrator) {
			return;
		}

		$databaseMigratorFactory = new DatabaseMigratorFactory();
		self::$databaseMigrator = $databaseMigratorFactory->create();
	}

	/**
	 * @return void
	 */
	protected function bootModels()
	{
		// TODO: Remove this when Laravel fixes the issue with model booting in tests
		if (self::$forceBoot) {
			$this->setUpModels();
		} else {
			self::$forceBoot = true;
		}
	}

	/**
	 * @return void
	 */
	protected function setUpModels()
	{
	}

	/**
	 * @return void
	 */
	protected function tearDownModels()
	{
	}

	/**
	 * @param mixed|$abstract
	 * @param object|null $mockInstance
	 * @return \Mockery\MockInterface
	 */
	protected function mockAppInstance($abstract, $mockInstance = null)
	{
		if ( ! $mockInstance) {
			$mockInstance = m::mock($abstract);
		}

		\App::instance($abstract, $mockInstance);

		return $mockInstance;
	}
}
