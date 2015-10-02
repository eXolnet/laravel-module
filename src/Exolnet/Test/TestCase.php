<?php namespace Exolnet\Test;

use DB;
use Exception;
use Exolnet\Test\Traits\AssertionsTrait;
use Faker\Factory as FakerFactory;
use Mockery as m;
use Route;

class TestCase extends \Illuminate\Foundation\Testing\TestCase {
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
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__ . '/../../../bootstrap/start.php';
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

		Route::enableFilters();

		self::bootModels();
	}

	/**
	 * @return void
	 */
	public function tearDown()
	{
		DB::disconnect();
		m::close();

		$this->tearDownModels();

		$this->app->reset();
		$this->app = null;

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
			$this->setupModels();
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
}
