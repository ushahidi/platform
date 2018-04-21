<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Data Provider Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Ushahidi\Core\Entity\DataProviderRepository;
use Ushahidi\Core\Entity\PostExportRepository;
use \Ushahidi\Core\Entity\FormAttributeRepository;

use GuzzleHttp\Promise\Promise;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\FormatterTrait;

use Ushahidi\Core\Entity\ExportJobRepository;

/* Simple console command that processes pending jobs in the DB */

class Ushahidi_Console_ProcessExportJobs extends Command {

    use UserContext;
	use FormatterTrait;

    private $data;
    private $postExportRepository;
    private $exportJobRepository;
    private $formAttributeRepository;


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

	public function setFormAttributeRepo(FormAttributeRepository $repo)
	{
		$this->formAttributeRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('processexports')
			->setDescription('Processes pending export jobs.')
			->addArgument('action', InputArgument::OPTIONAL, 'list, pending', 'pending')
			//->addOption('provider', ['p'], InputOption::VALUE_OPTIONAL, 'operate with a single provider')
			//->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'number of messages')
			//->addOption('all', ['a'], InputOption::VALUE_NONE, 'all providers, including disabled (use with list)')
			;
	}

    //display all jobs, including pending, failed, and successful
   protected function executeList(InputInterface $input, OutputInterface $output)
   {
       $jobs = $this->getJobs($input, $output);
       foreach ($jobs as $job)
       {
           $list[] = [
               'ID'       => $job->id,
               'Fields'     => $job->fields,
               'Filters'    => $job->filters,
               'Status'     => $job->status,
           ];
       }
       return $list;
   }

	protected function getJobs()
	{
        // @TODO: handle input options, e.g., to find just pending jobs
		$jobs = $this->exportJobRepository->getAllJobs();
        return $jobs;
	}

    protected function updateJobWithResponse($jobId, $responseInfo)
	{
        // update that job with success/failure
        $resultStatus = 'pending';
        if (array_key_exists('success', $responseInfo))
        { if($responseInfo['success'] == TRUE)
            {
                $resultStatus = 'SUCCESS';
            }else {
                $resultStatus = 'FAILED';
            }
        }

        $jobEntity = $this->exportJobRepository->get($jobId);
        // @TODO: get accessible path for this URL from config!
         $accessiblePath = 'http://192.168.33.110/media/uploads/';

        $jobEntity->setState(['id' => $jobId, 'status' => $resultStatus, 'url' => $accessiblePath.$responseInfo['file'] ]);
        $this->exportJobRepository->update($jobEntity);
        return $resultStatus;
	}

	protected function executePending(InputInterface $input, OutputInterface $output)
	{
        $pendingJobs = $this->getJobs();
        $jobsProcessed = ['count' => 0, 'job_info' => []];

		foreach ($pendingJobs as $job)
		{
            if ($job->status == 'pending')
            {
                //do a full export without limits
                $exportResponse = $this->doExportAsCli($job->id);
                $status = $this->updateJobWithResponse($job->id, $exportResponse);
                $jobsProcessed['count']++;
                array_push($jobsProcessed['job_info'], [$job->id => $status]);
            }
        }
       $this->handleResponse($jobsProcessed, $output, 'json');
	}


    protected function doExportAsCli($job_id)
    {
        $exportCommand = $this->getApplication()->find('exporter');
        $output = new BufferedOutput();

		// Construct console command input
		$input = new ArrayInput(array(
			'--offset' => 0,
			'--job' => $job_id,
			'--include_header' => 'true',
        ));

         //$greetInput = new ArrayInput($arguments);
         $returnCode = $exportCommand->run($input, $output);
         $executionResults['success'] = false;
         if($returnCode == 0)
         {
             $executionResults['success'] = true;
             $response = json_decode($output->fetch());
             $executionResults['file'] = $response[0]->file;
        }
        return $executionResults;
    }

    /*
    //abridged version of PostExport export
    // @TODO: go back and DRY this up, if we don't need to dupe portions of this
    protected function doExportOfJob( $jobId )
	{
        $succeeeded = FALSE;
		// Construct a Search Data object to hold the search info
		$data = $this->data->get('search');
		$offset = 0;
		$add_header = true; //always add the header
		$format = 'csv';

		// Set the baseline filter parameters
		$filters = [
			//'limit' => $limit,
			'offset' => $offset,
			'exporter' => true
		];

		// Load the export job
        $job = $this->exportJobRepository->get($jobId);

		$this->getSession()->setUser($job->user_id);
		// Merge the export job filters with the base filters
		if ($job->filters) {
			$filters = array_merge($filters, $job->filters);
		}

		// Set the fields that should be included if set
		if ($job->fields) {
			$data->include_attributes = $job->fields;
		}
        // Set the filters that should be included if set
		foreach ($filters as $key => $filter) {
			$data->$key = $filter;
		}

		$this->postExportRepository->setSearchParams($data);
		$posts = $this->postExportRepository->getSearchResults();

		$this->formatter->setAddHeader($add_header);
		//fixme add post_date
		$form_ids = $this->postExportRepository->getFormIdsForHeaders();
		$attributes = $this->formAttributeRepository->getByForms($form_ids);

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

		$header_row = $this->formatter->createHeading($job->header_row, $posts);
		$this->formatter->setHeading($header_row);

        $formatter = $this->formatter;

		$file = $formatter->__invoke($posts, $keyAttributes);

        // @TODO: is there a condition when this will result in failure?!
        //  Add try/catches/finally above, at least, to trigger FAILED updates
        if (is_string($file->file) && $file->size > 0)
        {
            $succeeeded = TRUE;
        }

        $result = [
                        'success' => $succeeeded,
    			        'file' => strtolower(uniqid()).$file->file,
				  ];

		return $result;
	}*/


}
