<?php namespace Exolnet\Console\Commands;

use Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class QueueSetup extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'queue:setup {--f|force}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Configure the queue worker for this project.';

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * RedisSetup constructor.
	 *
	 * @param \Illuminate\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem)
	{
		parent::__construct();

		$this->filesystem = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->checkDependencies();

		if ($this->createConfiguration()) {
			$this->updateSupervisor();
		}
	}

	/**
	 * @return string
	 */
	private function getIdentifier()
	{
		return Str::slug(Config::get('log.app'), '_');
	}

	/**
	 * @return string
	 */
	private function getSupervisorConfig()
	{
		return rtrim(env('SUPERVISOR_CONFIG', '/etc/supervisor/conf.d'), '/');
	}

	/**
	 * @return string
	 */
	private function getConfigName()
	{
		$identifier   = $this->getIdentifier();
		$environment  = env('APP_ENV');

		return $identifier .'_'. $environment;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	private function getBasePath($path = '')
	{
		$basePath = rtrim(env('QUEUE_DIRECTORY', base_path()), '/');

		return $basePath . ($path ? DIRECTORY_SEPARATOR.$path : $path);
	}

	/**
	 * @return void
	 */
	private function checkDependencies()
	{
		$supervisorConfig = $this->getSupervisorConfig();

		if ( ! $this->filesystem->exists($supervisorConfig)) {
			throw new RuntimeException('Could not find the directory "'. $supervisorConfig .'". Is supervisor installed?');
		} elseif ( ! $this->filesystem->isWritable($supervisorConfig)) {
			throw new RuntimeException('You don\'t have the write access to the folder "'. $supervisorConfig .'".');
		}

		if ( ! $this->getIdentifier()) {
			throw new RuntimeException('You need to define an application name (configuration variable "log.app").');
		}

		if ( ! env('QUEUE_USER')) {
			throw new RuntimeException('You need to define the user that will execute the worker (environment variable QUEUE_USER).');
		}
	}

	/**
	 * @return bool
	 */
	private function createConfiguration()
	{
		$configuration = $this->generateConfiguration();

		$this->comment($configuration);

		if ( ! $this->confirmToProceed('Is this queue configuration correct?')) {
			return false;
		}

		$configDirectory = $this->getSupervisorConfig() .'/'. $this->getConfigName();
		$configFile      = $configDirectory .'/queue.conf';

		if ( ! $this->shouldOverwriteConfig($configFile, $configuration)) {
			return false;
		}

		if ( ! $this->filesystem->exists($configDirectory)) {
			$this->filesystem->makeDirectory($configDirectory);
		}

		$this->filesystem->put($configFile, $configuration);

		return true;
	}

	/**
	 * @return string
	 */
	private function generateConfiguration()
	{
		$identifier   = $this->getIdentifier();
		$environment  = env('APP_ENV');

		$name         = $identifier .'.'. $environment .'.queue';
		$directory    = $this->getBasePath();
		$processCount = env('QUEUE_PROCESS_COUNT', 1);
		$user         = env('QUEUE_USER');
		$autoStart    = env('QUEUE_AUTO_START', true) ? 'true' : 'false';
		$autoRestart  = env('QUEUE_AUTO_RESSTART', true) ? 'true' : 'false';
		$stopAsGroup  = env('QUEUE_STOP_AS_GROUP', true) ? 'true' : 'false';
		$killAsGroup  = env('QUEUE_KILL_AS_GROUP', true) ? 'true' : 'false';
		$stderr       = $this->getBasePath('storage/logs/queue.err.log');
		$stdout       = $this->getBasePath('storage/logs/queue.out.log');

		$commandPhp   = env('QUEUE_COMMAND_PHP', 'php');
		$timeout      = env('QUEUE_TIMEOUT', 0);
		$sleep        = env('QUEUE_SLEEP', 5);

		$command      = $commandPhp. ' artisan queue:listen --timeout='. $timeout .' --sleep='. $sleep;

		return <<<EOC
[program:{$name}]
directory={$directory}
command={$command}
process_name=%(program_name)s-%(process_num)s
numprocs={$processCount}
user={$user}
autostart={$autoStart}
autorestart={$autoRestart}
stopasgroup={$stopAsGroup}
killasgroup={$killAsGroup}
stderr_logfile={$stderr}
stdout_logfile={$stdout}
EOC;
	}

	/**
	 * @return void
	 */
	private function updateSupervisor()
	{
		$process = new Process('supervisorctl update');
		$process->run();

		if ( ! $process->isSuccessful()) {
			$this->line('<bg=yellow>You need to run the command "supervisorctl update" to reload supervisor\'s configuration.</>');
		}
	}

	/**
	 * @param string $warning
	 * @return bool
	 */
	private function confirmToProceed($warning)
	{
		if ($this->option('force')) {
			return true;
		}

		return $this->confirm($warning, true);
	}

	/**
	 * @param string $file
	 * @param string $expectedContent
	 * @return bool
	 */
	private function shouldOverwriteConfig($file, $expectedContent)
	{
		if ( ! $this->filesystem->exists($file)) {
			return true;
		}

		$actualContent = $this->filesystem->get($file);

		if ($actualContent === $expectedContent) {
			$this->info('The worker configuration is already defined.');
			return false;
		}

		return $this->confirmToProceed('A configuration file already exists at "'. $file .'". Do you want to overwrite it?');
	}
}
