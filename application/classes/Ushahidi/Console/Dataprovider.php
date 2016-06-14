<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Data Provider Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Ushahidi\Core\Entity\DataProviderRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_Dataprovider extends Command {

	public function setRepo(DataProviderRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function configure()
	{
		$this
			->setName('dataprovider')
			->setDescription('List, fetch, and send messages from data providers')
			->addArgument('action', InputArgument::OPTIONAL, 'list, incoming, outgoing', 'list')
			->addOption('provider', ['p'], InputOption::VALUE_OPTIONAL, 'operate with a single provider')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of messages')
			->addOption('all', ['a'], InputOption::VALUE_NONE, 'all providers, including disabled (use with list)')
			;
	}

	protected function get_providers(InputInterface $input, OutputInterface $output = NULL)
	{
		if ($provider = $input->getOption('provider'))
		{
			$providers = [$this->repo->get($provider)];
		}
		else
		{
			$providers = $this->repo->all(!$input->getOption('all'));
		}
		return $providers;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		$providers = $this->get_providers($input, $output);

		$list = [];
		foreach ($providers as $id => $provider)
		{
			$list[] = [
				'Name'        => $provider->name,
				'Version'      => $provider->version,
				'Capabilities' => implode(', ', array_keys(array_filter($provider->services))),
			];
		}
		return $list;
	}

	protected function executeIncoming(InputInterface $input, OutputInterface $output)
	{
		$providers = $this->get_providers($input, $output);
		$limit = $input->getOption('limit');

		$totals = [];

		foreach ($providers as $provider)
		{
			$totals[] = [
				'Provider' => $provider->name,
				'Total'    => \DataProvider::factory($provider->id)->fetch($limit),
			];
		}

		return $totals;
	}

	protected function executeOutgoing(InputInterface $input, OutputInterface $output)
	{
		$providers = $this->get_providers($input, $output);
		$limit = $input->getOption('limit');

		// Hack: always include email no matter what!
		if (!isset($providers['email'])) {
			$providers['email'] = $this->repo->get('email');
		}

		$totals = [];
		foreach ($providers as $id => $provider)
		{
			$totals[] = [
				'Provider' => $provider->name,
				'Total'    => \DataProvider::process_pending_messages($limit, $id)
			];
		}

		return $totals;
	}
}
