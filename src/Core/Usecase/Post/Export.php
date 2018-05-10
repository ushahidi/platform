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
	protected $filters;
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
	 * @param $filters
	 * @param null $job_filters
	 * @return array
	 * Construct a filters object
	 */
	public function constructFilters($filters, $job_filters = null)
	{
		// Set the baseline filter parameters
		$filters = [
			'limit' => $filters['limit'],
			'offset' => $filters['offset'],
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
		$data = $this->search;

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

	/**
	 * Get the attributes we will use for the CSV header
	 * and create an assoc array like
	 * {'attribute_key': attribute, '2ndkey' : attribute}
	 * that we can use for the heading formatting
	 * @param $attributes
	 * @return array
	 */
	private function getAttributesWithKeys($attributes)
	{
		/**
		 * Get the attributes we will use for the CSV header
		 * and create an assoc array like
		 * {'attribute_key': attribute, '2ndkey' : attribute}
		 * that we can use for the heading formatting
		 */
		$keyAttributes = [];
		foreach ($attributes as $key => $item) {
			$keyAttributes[$item['key']] = $item;
		}
		return $keyAttributes;
	}
	public function interact()
	{

		//FIXME inject
		$this->session = service('session');
		// Load the export job
		$job = $this->exportJobRepository->get($this->filters['job_id']);
		// load the user from the job into the 'session'
		$this->session->setUser($job->user_id);
		// merge filters from the controller/cli call with the job's saved filters
		$filters = $this->constructFilters($this->filters, $job->filters);
		// get filters for the search object
		$data = $this->constructSearchData($job, $filters);
		$this->postExportRepository->setSearchParams($data);
		$attributes = $this->formAttributeRepository->getExportAttributes($data->include_attributes);
		$keyAttributes = $this->getAttributesWithKeys($attributes);
		/**
		 * get the search results based on filters
		 * and retrieve the metadata for each of the posts
		**/
		$posts = $this->postExportRepository->getSearchResults();
		foreach ($posts as $idx => $post) {
			// Retrieved Attribute Labels for Entity's values
			$post = $this->postExportRepository->retrieveMetaData($post->asArray(), $keyAttributes);
			$posts[$idx] = $post;
		}
		/**
		 * update the header attributes
		 * in the job table so we know which headers to
		 * use in other chunks of the export
		 */
		if (empty($job->header_row)) {
			$job->setState(['header_row' => $attributes]);
			$this->exportJobRepository->update($job);
		}
		/**
		 * set 'add header' in the formatter
		 * so it knows how to return the results
		 * for the csv (with or without a header row)
		 */
		$this->formatter->setAddHeader($this->filters['add_header']);
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
}
