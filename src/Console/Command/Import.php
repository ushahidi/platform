<?php

/**
 * Ushahidi Import Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use SplFileObject;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LoggerInterface;

use Ushahidi\Console\Command;
use League\Csv\Reader;
use Ushahidi\Core\Tool\MappingTransformer;
use Ushahidi\Core\Usecase\ImportUsecase;

class Import extends Command
{

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
			->setDescription('Import data')
			->addArgument('action', InputArgument::OPTIONAL, 'list, run', 'list')
			// @todo provide allowed types list
			->addOption('type', ['t'], InputOption::VALUE_OPTIONAL, 'import source type', 'csv')
			->addOption('file', ['f'], InputOption::VALUE_REQUIRED, 'Source filename')
			->addOption('map', ['m'], InputOption::VALUE_REQUIRED, 'Source-Destination field mapping')
			->addOption('values', [], InputOption::VALUE_REQUIRED, 'Static field values')
			->addOption('limit', [], InputOption::VALUE_OPTIONAL, 'Number of records to import')
			->addOption('offset', [], InputOption::VALUE_OPTIONAL, 'Offset to start importing from')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Supported Types' => 'CSV'
			]
		];
	}

	protected function executeRun(InputInterface $input, OutputInterface $output)
	{
		// Get the filename
		$filename = $input->getOption('file');

		// Load mapping and pass to transformer
		$map = file_get_contents($input->getOption('map'));
		$this->transformer->setMap(json_decode($map, true));

		// Load fixed values and pass to transformer
		$values = file_get_contents($input->getOption('values'));
		$this->transformer->setFixedValues(json_decode($values, true));

		// Get CSV reader
		$reader = $this->getReader($input->getOption('type'));

		// Set limit..
		if ($limit = $input->getOption('limit')) {
			$reader->setLimit($limit);
		}
		// .. and offset
		if ($offset = $input->getOption('offset')) {
			$reader->setOffset($offset);
		}

		// Get the traversable results
		$payload = $reader->process($filename);

		// Get the usecase and pass in authorizer, payload and transformer
		$this->usecase
			->setPayload($payload)
			->setTransformer($this->transformer);

		// Execute the import
		return $this->usecase->interact();
	}

	/**
	 * Map of readers
	 * @var [FileReader, ...]
	 */
	protected $readerMap = [];

	public function setReaderMap($map)
	{
		$this->readerMap = $map;
	}

	protected function getReader($type)
	{
		return $this->readerMap[$type]();
	}

	/**
	 * @var Ushahidi\Core\Tool\MappingTransformer
	 */
	protected $transformer;

	public function setTransformer(MappingTransformer $transformer)
	{
		$this->transformer = $transformer;
	}

	/**
	 * @var Ushahidi\Core\Usecase\ImportUsecase
	 * @todo  support multiple entity types
	 */
	protected $usecase;

	public function setImportUsecase(ImportUsecase $usecase)
	{
		$this->usecase = $usecase;
	}
}
