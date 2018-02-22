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
use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\FormatterTrait;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use League\Flysystem\Util\MimeType;

class Ushahidi_Console_PostExporter extends Command
{

	use UserContext;
	use FormatterTrait;

    private $data;
	private $postExportRepository;
	private $exportJobRepository;
	private $fs;

	public function setFileSystem(Filesystem $fs)
	{
		$this->fs = $fs;
	}

	public function setDatabase(Database $db)
	{
		$this->db = $db;
	}

	public function setExportJobRepo(ExportJobRepository $repo)
	{
		$this->exportJobRepository = $repo;
	}

	public function setDataFactory(DataFactory $data)
	{
		$this->data = $data;
	}

	public function setPostExportRepo(PostExportRepository $repo)
	{
		$this->postExportRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('exporter')
			->setDescription('Export Posts')
			->addArgument('action', InputArgument::REQUIRED, 'list, export')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'limit')
			->addOption('offset', ['o'], InputOption::VALUE_OPTIONAL, 'offset')
			->addOption('job', ['j'], InputOption::VALUE_OPTIONAL, 'job')
			->addOption('add-header', ['h'], InputOption::VALUE_OPTIONAL, 'add-header')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'export'
			]
		];
	}

	protected function executeExport(InputInterface $input, OutputInterface $output)
	{
		// Construct a Search Data objec to hold the search info
        $data = $this->data->get('search');

		// Get CLI params
		$limit = $input->getOption('limit', 100);
		$offset = $input->getOption('offset', 0);
		$job_id = $input->getOption('job', null);
		$add_header = $input->getOption('add-header', true);

		// At the moment there is only CSV format
		$format = 'csv';

		// Set the baseline filter parameters
        $filters = [
            'limit' => $limit,
            'offset' => $offset,
			'exporter' => true
		];

		if ($job_id) {
			
			// Load the export job
			$job = $this->exportJobRepository->get($job_id);
			
			// Merge the export job filters with the base filters
			if ($job->filters) {
				$filters = array_merge($filters, $job->filters);
			}
			
			// Set the fields that should be included if set
			if ($job->fields) {
				$data->include_attributes = $job->fields;
			}
		}

        foreach ($filters as $key => $filter) {
            $data->$key = $filter;
		}

        $this->postExportRepository->setSearchParams($data);
		
        $posts = $this->postExportRepository->getSearchResults();

		// // ... remove any entities that cannot be seen
		foreach ($posts as $idx => $post) {

			// Retrieved Attribute Labels for Entity's values
			$post = $this->postExportRepository->retrieveColumnNameData($post->asArray());

			$posts[$idx] = $post;
		}

		service("formatter.entity.post.$format")->setFileSystem($this->fs);
		$file = service("formatter.entity.post.$format")->__invoke($posts, $add_header);
		
		$response = [
			[
				'file' => $file->file,
			]
		];

		$this->handleResponse($response, $output, 'json');
	}
}
