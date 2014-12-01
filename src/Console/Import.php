<?php

/**
 * Ushahidi Import Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;

class Import extends Command {

	/**
	 * Data Importers
	 * @var [Ushahidi\DataImport\Importer, ..]
	 */
	protected $importers;

	/**
	 * Set Data Importers
	 * @param [Ushahidi\DataImport\Importer, ..] $importers
	 */
	public function setImporters(Array $importers)
	{
		$this->importers = $importers;
	}

	protected function configure()
	{
		$this
			->setName('import')
			->setDescription('Import data from Ushahidi 2.x')
			->addArgument('action', InputArgument::OPTIONAL, 'import', 'import')
			// @todo provide allowed types list
			->addOption('type', ['t'], InputOption::VALUE_OPTIONAL, 'import source type', 'dbv2')
			// @todo only require this for dbv2 type
			->addOption('dsn', ['d'], InputOption::VALUE_REQUIRED, 'PDO Data Source Name (DSN) to connect to source db')
			->addOption('username', ['u'], InputOption::VALUE_REQUIRED, 'DB username')
			->addOption('password', ['p'], InputOption::VALUE_REQUIRED, 'DB password')
			//->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of messages')
			//->addOption('all', ['a'], InputOption::VALUE_NONE, 'all providers, including disabled (use with list)')
			;
	}

	protected function execute_import(InputInterface $input, OutputInterface $output)
	{
		$logger = new ConsoleLogger($output);

		// Invoke lazyNew to create the importer
		// @todo avoid handling DI object here :/
		$importer = $this->importers[$input->getOption('type')]();
		$importer->setLogger($logger);

		// @todo allow importer to define options and maybe ask interactive questions?
		// @todo validate input?

		$result = $importer->import($input->getOptions());

		return $result;
	}

}
