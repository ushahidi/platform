<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi User Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Exception\ValidatorException;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ushahidi_Console_User extends Command {

	public function setRepo(UserRepository $repo)
	{
		$this->repo = $repo;
	}

	public function setValidator(Validator $validator)
	{
		$this->validator = $validator;
	}

	protected function configure()
	{
		$this
			->setName('user')
			->setDescription('Create user accounts')
			->addArgument('action', InputArgument::REQUIRED, 'create')
			->addOption('username', ['u'], InputOption::VALUE_REQUIRED, 'username')
			->addOption('realname', null, InputOption::VALUE_OPTIONAL, 'realname')
			->addOption('email', ['e'], InputOption::VALUE_REQUIRED, 'email')
			->addOption('role', ['r'], InputOption::VALUE_OPTIONAL, 'role')
			->addOption('password', ['p'], InputOption::VALUE_REQUIRED, 'password')
			;
	}

	// Execute create action
	protected function executeCreate(InputInterface $input, OutputInterface $output)
	{
		$state = [
			'username' => $input->getOption('username'),
			'realname' => $input->getOption('realname') ?: null,
			'email' => $input->getOption('email'),
			// Default to creating an admin user
			'role' => $input->getOption('role') ?: 'admin',
			'password' => $input->getOption('password'),
		];
		
		if ( !$this->validator->check($state)) {
			throw new ValidatorException('Failed to validate user', $this->validator->errors());
		}

		$entity = $this->repo->getEntity();
		$entity->setState($state);
		$entity = $this->repo->create($entity);
	}
}
