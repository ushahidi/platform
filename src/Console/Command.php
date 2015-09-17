<?php
/**
 * Ushahidi Console Command
 *
 * For input format `some:command action` where the action will be called as:
 *
 *   $this->execute_$action($input, $output);
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\TableHelper;

abstract class Command extends ConsoleCommand
{

	// Execution router takes the action argument and uses it to reroute execution.
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Enforce a default action of list.
		$action  = $input->getArgument('action') ?: 'list';
		$execute = 'execute' . ucfirst($action);

		// Reroute to the specific action.
		$response = $this->$execute($input, $output);

		// Format the response and output
		$this->handleResponse($response, $output);
	}

	protected function handleResponse($response, OutputInterface $output)
	{
		if (is_array($response)) {
			// Display arrays as tables.
			$table = $this->getHelperSet()->get('table');

			$keys = array_keys($response);

			if (is_array(current($response))) {
				// Assume that an array of arrays is a result list.
				$table
					->setHeaders(array_keys(current($response)))
					->setRows($response);
			// Is the array associative?
			} elseif (array_keys($keys) !== $keys) {
				// Assume that an associative array is a single result.
				$table
					->setHeaders(array_keys($response))
					->addRow($response);
			} else {
				// Assume that an indexed array is a list of values.
				foreach ($response as $row) {
					$table->addRow(array($row));
				}
			}

			// Display the table using compact layout.
			$table
				->setLayout(TableHelper::LAYOUT_COMPACT)
				->render($output);
		} else {
			// Otherwise, just write the response.
			$output->writeln($response);
		}
	}
}
