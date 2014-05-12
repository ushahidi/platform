<?php defined('SYSPATH') or die('No direct script access');

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\TableHelper;

abstract class Ushahidi_Console_Command extends Command {

	// Execution router takes the action argument and uses it to reroute execution.
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Enforce a default action of list.
		$action  = $input->getArgument('action') ?: 'list';
		$execute = 'execute_' . $action;

		// Reroute to the specific action.
		$response = $this->$execute($input, $output);

		if (is_array($response))
		{
			// Display arrays as tables.
			$table = $this->getHelperSet()->get('table');

			if (is_array(current($response)))
			{
				// Assume that an array of arrays is a result list.
				$table
					->setHeaders(array_keys(current($response)))
					->setRows($response);
			}
			elseif (Arr::is_assoc($response))
			{
				// Assume that an associative array is a single result.
				$table
					->setHeaders(array_keys($response))
					->addRow($response);
			}
			else
			{
				// Assume that an indexed array is a list of values.
				foreach ($response as $row)
				{
					$table->addRow(array($row));
				}
			}

			// Display the table using compact layout.
			$table
				->setLayout(TableHelper::LAYOUT_COMPACT)
				->render($output);
		}
		else
		{
			// Otherwise, just write the response.
			$output->writeln($response);
		}
	}

	protected function get_user(InputInterface $input, OutputInterface $output = NULL)
	{
		// Username to user ID converter.
		// TODO: use the repo!
		$userid = function($username)
		{
			if (!$username)
				return NULL;

			return DB::select('id')
				->from('users')
				->where('username', '=', $username)
				->execute()
					->get('id')
					;
		};

		$username = $input->getOption('user');
		$user = NULL;

		if ($username)
		{
			// Check that the given username exists.
			$user = $userid($username);
			if (!$user)
				throw new RuntimeException('Unknown user "' . $username . '"');
		}
		elseif ($output)
		{
			// If required, `$output` will be passed and we can interactively
			// request the user to provide a username.
			$ask = function($username) use ($userid)
			{
				// And check that the given username exists.
				$user = $userid($username);
				if (!$user)
					throw new RuntimeException('Unknown user "' . $user . '", please try again');

				return $user;
			};

			$user = $this->getHelperSet()->get('dialog')
				->askAndValidate($output, 'For which user? ', $ask)
				;
		}

		return $user;
	}
}
