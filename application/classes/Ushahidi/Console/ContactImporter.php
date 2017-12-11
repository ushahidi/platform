<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\FormatterTrait;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;


class Ushahidi_Console_ContactImporter extends Command
{

	use UserContext;
	use FormatterTrait;

	private $userRepository;
	private $contactRepository;
	private $existing_contacts;
	private $existing_contacts_lookup = array();

	public function setDataFactory(DataFactory $data)
	{
		$this->data = $data;
	}

	public function setUserRepo(UserRepository $repo)
	{
		$this->userRepository = $repo;
	}

	public function setContactRepo(ContactRepository $repo)
	{
		$this->contactRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('contact')
			->setDescription('Contact functions')
            ->addArgument('action', InputArgument::OPTIONAL, 'list, import', 'list')
            ->addOption('filepath', ['f'], InputOption::VALUE_REQUIRED, 'Source filepath')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'import'
			]
		];
	}

	protected function setExistingContacts()
	{
		$data = $this->data->get('search');
		$this->contactRepository->setSearchParams($data);
		$contacts = $this->contactRepository->getSearchResults();
		foreach ($contacts as $contact) {
			array_push($this->existing_contacts_lookup, $contact->contact);
			$this->existing_contacts[$contact->contact] = $contact;
		}

	}

	protected function executeImport(InputInterface $input, OutputInterface $output)
	{
		ini_set('auto_detect_line_endings', 1);
		$this->setExistingContacts();
		$total = 0;
		$failed = 0;

        $filepath = $input->getOption('filepath');

		$csv = Reader::createFromPath($filepath, 'r');
		
        $csv->setOffset(1);
        $nbInsert = $csv->each(function ($row) use ($total) {
			// Probably should be validating :/
			
			$user = $this->userRepository->getEntity([
				'realname' => $row[0]
			]);
			$user_id = $this->userRepository->create($user);

			if ($this->contactExists($row[1])) {
				$contact = $this->getContact($row[1]);
				$state = [
					'user_id' => $user_id
				];
				$this->contactRepository->update($contact->setState($state));
			} else {
				$contact = $this->contactRepository->getEntity([
					'contact' => $row[1],
					'type' => $row[2],
					'dataprovider' => $row[3],
					'user_id' => $user_id
				]);
				$this->contactRepository->create($contact);
			}
			$total += 1;
			return true;
        });

		$response = [
			[
				'Message' => sprintf('%d contacts were imported, %d contacts failed to import', $total, $failed)
			]
		];

		$this->handleResponse($response, $output);
	}

	protected function contactExists($contact)
	{
		return in_array($contact, $this->existing_contacts_lookup);
	}

	protected function getContact($contact)
	{
		return $this->existing_contacts[$contact];
	}
}
