<?php

/**
 * Ushahidi Role Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console;

use Ushahidi\Core\Entity\RoleRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Role extends Command
{
	public function setRepo(RoleRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function configure()
	{
		$this
			->setName('role')
			->setDescription('Create roles')
			->addArgument('action', InputArgument::OPTIONAL, 'list, create', 'list')
			->addOption('role', ['r'], InputOption::VALUE_REQUIRED, 'role')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'create'
			]
		];
	}

	protected function executeCreate(InputInterface $input, OutputInterface $output)
	{
		// Default to creating an owner role
		$role = $input->getOption('role') ?: 'owner';
		$display_name = ucfirst($role);

		$state = [
			// Default to creating an owner role
			'name' => $role,
			'display_name' => $display_name
		];

		$entity = $this->repo->getEntity();
		$entity->setState($state);
		$this->repo->create($entity);

		return [
			[
				'Message' => sprintf('Role %s was created successfully', $role)
			]
		];
	}
}
