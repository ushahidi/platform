<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi OAuth:Client Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_OAuth_Client extends Command {

	// todo: put me in a repo!
	public static function db_list($client = null)
	{
		$query = DB::select()
			->from('oauth_clients');

		if ($client)
		{
			$query->where('id', '=', $client);
		}

		return $query->execute()->as_array();
	}

	// todo: put me in a repo!
	public static function db_create(array $data)
	{
		$query = DB::insert('oauth_clients')
			->columns(array_keys($data))
			->values(array_values($data))
			;

		list($id, $count) = $query->execute();

		return $id;
	}

	// todo: put me in a repo!
	public static function db_delete($client)
	{
		$query = DB::delete('oauth_clients')
			->where('id', '=', $client);

		return $query->execute();
	}

	protected function configure()
	{
		$this
			->setName('oauth:client')
			->setDescription('List, create, and delete OAuth clients')
			->addArgument('action', InputArgument::OPTIONAL, 'list, create, or delete', 'list')
			->addOption('client', ['c'], InputOption::VALUE_OPTIONAL, 'client id')
			->addOption('name', [], InputOption::VALUE_OPTIONAL, 'client name')
			->addOption('secret', ['s'], InputOption::VALUE_OPTIONAL, 'secret key')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		$client = $input->getOption('client');
		return static::db_list($client);
	}

	protected function executeCreate(InputInterface $input, OutputInterface $output)
	{
		$client = $input->getOption('client');
		$name   = $input->getOption('name');
		$secret = $input->getOption('secret');

		if (!$client)
		{
			// We can't use the generic `get_client()` for **creation**,
			// because we need to verify that the user does **not** exist.
			$clients = Arr::pluck(self::db_list(), 'id');
			$ask = function($client) use ($clients)
			{
				if (in_array($client, $clients))
					throw new RuntimeException('Client "' . $client . '" already exists, try another name');

				return $client;
			};

			$client = $this->getHelperSet()->get('dialog')
				->askAndValidate($output, 'Enter id of new client: ', $ask, FALSE)
				;
		}

		if (!$name)
			$name = $client;

		if (!$secret)
			$secret = Text::random('distinct', 24);

		static::db_create([
			'id'     => $client,
			'secret' => $secret,
			'name'   => $name,
			]);

		$input->setOption('client', $client);

		return $this->executeList($input, $output);
	}

	protected function getClient(InputInterface $input, OutputInterface $output = NULL)
	{
		$client = $input->getOption('client');

		if (!$client AND $output)
		{
			// If no client was given, and `$output` is passed, we can ask for
			// the user interactively and validate it against the known clients.
			$clients = Arr::pluck(self::db_list(), 'id');
			$ask = function($client) use ($clients)
			{
				if (!in_array($client, $clients))
					throw new RuntimeException('Unknown client "' . $client . '", valid options are: ' . implode(', ', $clients));

				return $client;
			};

			$client = $this->getHelperSet()->get('dialog')
				->askAndValidate($output, 'For which client? ', $ask, FALSE, NULL, $clients)
				;
		}

		return $client;
	}

	protected function executeDelete(InputInterface $input, OutputInterface $output)
	{
		$client = $this->getClient($input, $output);

		if (static::db_delete($client))
			return "Deleted <info>{$client}</info>";

		// TODO: This should result in an error return (code 1) but would
		// require writing directly to output, rather than passing control back
		// to `Command::execute`.
		return "Client <error>{$client}</error> was not found";
	}
}
