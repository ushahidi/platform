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
use \Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\UserContext;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use League\Flysystem\Util\MimeType;
use Aura\Di\Container;

class Ushahidi_Console_PostExporter extends Command
{

	use UserContext;
	use FormatterTrait;

	private $data;
	private $postExportRepository;
	private $exportJobRepository;
	private $formAttributeRepository;

	public function __construct()
	{
		parent::__construct();

	}

	public function setDatabase(Database $db)
	{
		$this->db = $db;
	}

	public function setExportJobRepo(ExportJobRepository $repo)
	{
		$this->exportJobRepository = $repo;
	}


	public function setFormAttributeRepo(FormAttributeRepository $repo)
	{
		$this->formAttributeRepository = $repo;
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
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'limit', 100)
			->addOption('offset', ['o'], InputOption::VALUE_OPTIONAL, 'offset', 0)
			->addOption('job', ['j'], InputOption::VALUE_OPTIONAL, 'job', null)
			->addOption('include_header', ['ih'], InputOption::VALUE_OPTIONAL, 'include_header', 1)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Construct a Search Data objec to hold the search info
		$data = $this->data->get('search');

		// Get CLI params
		$limit = $input->getOption('limit');
		$offset = $input->getOption('offset');
		$job_id = $input->getOption('job');
		$add_header = $input->getOption('include_header');

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

			$this->getSession()->setUser($job->user_id);
			// Merge the export job filters with the base filters
			if ($job->filters) {
				$filters = array_merge($filters, $job->filters);
			}

			// Set the fields that should be included if set
			if ($job->fields) {
				$data->include_attributes = $job->fields;
			}
		}

        /**
         * Setup the fields as filters by the form they belong to.
         * If the user selected only fields from some forms, we should not return all the forms
         * even if they did not have a form filter selected (since they can't filter that in the UI right now)
         */
        $form_ids_by_attributes = $this->formAttributeRepository->getFormsByAttributes($data->include_attributes);
		if (!empty($form_ids_by_attributes)) {
		    $filters['form'] = array_unique(
                array_merge(
                    isset($filters['form']) ? $filters['form'] : [],
                    array_map(function ($itm) { return intval($itm); } , $form_ids_by_attributes)
                )
            );
        }
        foreach ($filters as $key => $filter) {
			$data->$key = $filter;
		}
		$this->postExportRepository->setSearchParams($data);
		$posts = $this->postExportRepository->getSearchResults();
		$this->formatter->setAddHeader($add_header);
		//fixme add post_date


		$form_ids = $this->postExportRepository->getFormIdsForHeaders();
		$attributes = $this->formAttributeRepository->getByForms($form_ids, $data->include_attributes);

		$keyAttributes = [];
		foreach($attributes as $key => $item)
		{
			$keyAttributes[$item['key']] = $item;
		}

		// // ... remove any entities that cannot be seen
		foreach ($posts as $idx => $post) {
			// Retrieved Attribute Labels for Entity's values
			$post = $this->postExportRepository->retrieveMetaData($post->asArray(), $keyAttributes);
			$posts[$idx] = $post;
		}
		
		if (empty($job->header_row)) {
			$job->setState(['header_row' => $attributes]);
            $this->exportJobRepository->update($job);
		}

		$header_row = $this->formatter->createHeading($job->header_row);
		
		$this->formatter->setHeading($header_row);
		$formatter = $this->formatter;
		/**
		 * KeyAttributes is sent instead of the header row because it contains
		 * the attributes with the corresponding features (type, priority) that
		 * we need for manipulating the data
		 */
		$file = $formatter($posts, $keyAttributes);

		$response = [
			[
				'file' => $file->file,
			]
		];

		$this->handleResponse($response, $output, 'json');
	}
}
