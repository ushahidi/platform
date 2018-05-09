<?php

/**
 * Ushahidi Platform Post Export Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\App\Repository\ExportJobRepository;
use Ushahidi\App\Repository\Form\AttributeRepository;
use Ushahidi\App\Repository\Post\ExportRepository;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase\SearchUsecase;

class Export extends SearchUsecase
{
	private $payload;
	private $session;
	private $data;
	private $postExportRepository;
	private $exportJobRepository;
	private $formAttributeRepository;


	// provides setFormatter to assign the formatter the usecase will use
	use FormatterTrait;

	// - VerifyParentLoaded for checking that the parent exists
	use VerifyParentLoaded;

	public function setExportJobRepository(ExportJobRepository $repo)
	{
		$this->exportJobRepository = $repo;//service('repository.export_job');
	}

	public function setFormAttributeRepository(AttributeRepository $repo)
	{
		$this->formAttributeRepository = $repo; //service('repository.form_attribute');
	}

	public function setPostExportRepository(ExportRepository $repo)
	{
		$this->postExportRepository = $repo; //service('repository.posts_export');
	}

	/**
	 * @param $payload
	 * @param null $job_filters
	 * @return array
	 * Construct a filters object
	 */
	public function constructFilters($payload, $job_filters = null)
	{
		// Set the baseline filter parameters
		$filters = [
			'limit' => $payload['limit'],
			'offset' => $payload['offset'],
		];
		// Merge the export job filters with the base filters
		if ($job_filters) {
			$filters = array_merge($filters, $job_filters);
		}
		return $filters;
	}

	/**
	 * @param $job
	 * @param $filters
	 * @return mixed
	 * Construct a Search Data object to hold the search info
	 */
	public function constructSearchData($job, $filters)
	{
		$data = $this->data->get('search');

		// Set the fields that should be included if set
		if ($job->fields) {
			$data->include_attributes = $job->fields;
		}

		// set the filters that should be used
		foreach ($filters as $key => $filter) {
			$data->$key = $filter;
		}

		return $data;
	}

	public function interact()
	{

		//FIXME inject
		$this->data = service('factory.data');
		$this->session = service('session');

		$job_id = $this->payload['job_id'];
		$add_header = $this->payload['add_header'];
		// Load the export job
		$job = $this->exportJobRepository->get($job_id);

		// load the user from the job into the 'session'
		$this->session->setUser($job->user_id);
		$filters = $this->constructFilters($this->payload, $job->filters);
		$data = $this->constructSearchData($job, $filters);

		$this->postExportRepository->setSearchParams($data);
		$attributes = $this->formAttributeRepository->getExportAttributes($data->include_attributes);

		$keyAttributes = [];
		foreach ($attributes as $key => $item) {
			$keyAttributes[$item['key']] = $item;
		}

		$posts = $this->postExportRepository->getSearchResults();
		$this->formatter->setAddHeader($add_header);

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
		return [
			'results' => [
				[
					'file' => $file->file,
				]
			]
		];
	}


	/**
	 * Inject a repository that can create entities.
	 *
	 * @todo  setPayload doesn't match signature for other usecases
	 *
	 * @param  $repo Iterator
	 * @return $this
	 */
	public function setPayload($payload)
	{
		$this->payload = $payload;
		return $this;
	}
}
