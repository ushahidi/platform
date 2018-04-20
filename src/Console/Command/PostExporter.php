<?php

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Core\Entity\ExportJobRepository;
use \Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\UserContextService;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\UserContext;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostExporter extends Command
{

	use FormatterTrait;

	private $data;
	private $postExportRepository;
	private $exportJobRepository;
	private $formAttributeRepository;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'export';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'export {--limit=100} {--offset=0} {--job} {--include-header=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export posts';

	public function handle()
	{
		// @todo inject
        $this->exportJobRepository = service('repository.export_job');
        $this->formAttributeRepository = service('repository.form_attribute');
        $this->data = service('factory.data');
        $this->postExportRepository = service('repository.posts_export');
        $this->session = service('session');
        $this->formatter = service('formatter.entity.post.csv');

        // Construct a Search Data object to hold the search info
        $data = $this->data->get('search');

        // Get CLI params
		$limit = $this->option('limit');
        $offset = $this->option('offset');
        $job_id = $this->option('job');
        $add_header = $this->option('include-header');

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

			$this->session->setUser($job->user_id);
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
		$this->formatter->setAddHeader($add_header);
		//fixme add post_date
		$form_ids = $this->postExportRepository->getFormIdsForHeaders();
		$attributes = $this->formAttributeRepository->getByForms($form_ids);

		$keyAttributes = [];
		foreach ($attributes as $key => $item) {
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

        $this->line(json_encode($response));
	}
}
