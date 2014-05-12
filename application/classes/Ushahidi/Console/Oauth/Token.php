<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi OAuth:Token Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_OAuth_Token extends Ushahidi_Console_Oauth_Command {

	// todo: put me in a repo!
	public static function db_list($type, array $search = NULL)
	{
		$query = DB::select('*')
			->from("oauth_{$type}_tokens")
			;

		if (!empty($search['scopes']))
		{
			foreach ($search['scopes'] as $scope)
			{
				$query->where('scope', 'LIKE', '%' . $scope . '%');
			}
			unset($search['scopes']);
		}

		if ($search)
		{
			foreach ($search as $column => $value)
			{
				$query->where($column, '=', $value);
			}
		}

		return $query->execute()->as_array();
	}

	// todo: put me in a repo!
	public static function db_delete($type, array $search = NULL)
	{
		$query = DB::delete("oauth_{$type}_tokens");

		if (!$search)
			throw new RuntimeException("Refusing to delete without user or client search");

		foreach ($search as $column => $value)
		{
			$query->where($column, '=', $value);
		}

		return $query->execute();

	}

	// todo: put me in a repo!
	public static function db_clean($type)
	{
		$query = DB::delete("oauth_{$type}_tokens")
			->where('user_id', '=', NULL)
			;

		return $query->execute();

	}

	protected function configure()
	{
		$this
			->setName('oauth:token')
			->setDescription('List, create, and delete OAuth tokens')
			->addArgument('action', InputArgument::OPTIONAL, 'list, create, delete, or clean', 'list')
			->addOption('client', ['c'], InputOption::VALUE_OPTIONAL, 'client id')
			->addOption('user', ['u'], InputOption::VALUE_OPTIONAL, 'user id')
			->addOption('type', ['t'], InputOption::VALUE_OPTIONAL, 'access or refresh', 'access')
			->addOption('scope', ['s'], InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'requested scopes')
			;
	}

	protected function execute_create(InputInterface $input, OutputInterface $output)
	{
		$client = $this->get_client($input, $output);
		$user = $this->get_user($input, $output);
		$scopes = $this->get_scopes($input) ?: $this->supported_scopes();

		// Create a fake HTTP request for the OAuth server
		$query = [
			'client_id' => $client,
			'scope' => implode(' ', $scopes),
			'response_type' => 'token',
			'state' => 'console',
			];
		$request = new OAuth2\Request($query, ['REQUEST_METHOD' => 'POST']);
		$response = new OAuth2\Response;

		// Execute the request with the server.
		$this->server->handleAuthorizeRequest($request, $response, true, $user);

		// OAuth server responds by redirecting with the token in the URL.
		$location = $response->getHttpHeader('Location');

		// Extract the auth parameters from the redirect URL.
		//
		// *I have absolutely no idea what OAuth2 server returns the token in
		// the fragment part of the URL...*
		parse_str(parse_url($location, PHP_URL_FRAGMENT), $params);

		return $params;
	}

	protected function execute_list(InputInterface $input, OutputInterface $output)
	{
		$type = $this->get_type($input);
		$search = [
			// Get optional argumens for listing
			'user_id' => $this->get_user($input),
			'scopes' => $this->get_scopes($input),
			];

		return static::db_list($type, array_filter($search));
	}

	protected function execute_clean(InputInterface $input, OutputInterface $output)
	{
		$type = $this->get_type($input);
		$search = [
			// Get optional argumens for cleaning
			'client_id' => $this->get_client($input),
			'user_id' => $this->get_user($input),
			'scopes' => $this->get_scopes($input),
			];
		$count = static::db_clean($type, array_filter($search));
		return "Deleted <info>{$count}</info> invalid $type " . Inflector::plural('token', $count);
	}

	protected function execute_delete(InputInterface $input, OutputInterface $output)
	{
		$type = $this->get_type($input);
		$search = [
			// One of these is required.
			'client_id' => $this->get_client($input),
			'user_id' => $this->get_user($input),
			];
		$count = static::db_delete($type, array_filter($search));
		return "Deleted <info>{$count}</info> $type " . Inflector::plural('token', $count);
	}

	protected function get_type(InputInterface $input)
	{
		$type = $input->getOption('type');
		$types = ['access', 'refresh'];

		if (!in_array($type, $types))
			throw new RuntimeException('Invalid type "' . $type . '" given, valid types are: ' . implode(', ', $types));

		return $type;
	}

}
