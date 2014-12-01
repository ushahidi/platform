<?php

/**
 * Ushahidi Platform Data Importer for Ushahidi v2 Databases
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Importer;

use Ushahidi\DataImport\Importer;
use Psr\Log\LoggerInterface;

class DBv2Importer implements Importer
{

	/**
	 * Data Importers
	 * @var [Ushahidi\DataImport\Importer, ..]
	 */
	protected $steps;

	/**
	 * Set import steps
	 * @param [Ushahidi\DataImport\ImportStep, ..] $steps
	 */
	public function setImportSteps(Array $steps) {
		$this->steps = $steps;
	}

	/**
	 * Logger
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * Set logger
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	// Importer
	public function import(Array $options) {
		$output = [];

		$dsn = $options['dsn'];
		$username = $options['username'];
		$password = $options['password'];

		// Set up PDO connection
		// @todo should this be injected somehow? a factory?
		$connection = new \PDO($dsn, $username, $password);

		// Loop over steps
		foreach ($this->steps as $key => $stepDI)
		{
			// @todo avoid handling DI object here :/
			$step = $stepDI();

			// @todo ok to pass connection as a param?
			$result = $step->run([
				'connection' => $connection,
				'logger'     => $this->logger
			]);

			$output[$key] = [
				'processed' => $result->getTotalProcessedCount(),
				'time' => $result->getElapsed()->format('%s seconds'),
				'errors' => $result->getErrorCount(),
			];
		}

		return $output;
	}
}
