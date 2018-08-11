<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DumpBasicDB extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dumpDb:basic';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Dump the basic db structure and data for new installations.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//

		$dbConfig = Config::get('database')['connections']['mysql'];
		$mysqlDumpCredentialOptions = [
			'--user='. escapeshellarg($dbConfig['username']),
			'--password=' . escapeshellarg($dbConfig['password']),
			'--host=' . escapeshellarg($dbConfig['host'])
		];

		function getCommand($options, $mysqlDumpCredentialOptions){
			return 'mysqldump ' . implode(' ', array_merge($mysqlDumpCredentialOptions, $options));
		}

		$dumpStructureFilePath = Config::get('install.dbBasicStructureFile');
		$dumpDataFilePath = Config::get('install.dbBasicDataFile');
		$mysqlDumpStructureOptions = [
			'--add-drop-table',
			'--no-data',
			escapeshellarg($dbConfig['database']),
			' > ' . escapeshellarg($dumpStructureFilePath)
		];

		$mysqlDumpDataOptions = [
			'--no-create-info',
			escapeshellarg($dbConfig['database']),
			'config pages quizes',
			' > ' . escapeshellarg($dumpDataFilePath)
		];

		$dumpStructureCliCommand = getCommand($mysqlDumpStructureOptions, $mysqlDumpCredentialOptions);
		$dumpStructureOutput = exec($dumpStructureCliCommand);

		$dumpDataCliCommand = getCommand($mysqlDumpDataOptions, $mysqlDumpCredentialOptions);
		$dumpDataOutput = exec($dumpDataCliCommand);

		$this->info($dumpDataOutput);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(

		);
	}


}
