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
use Ushahidi\App\Repository\HXL\HXLFormAttributeHXLAttributeTagRepository;
use Ushahidi\App\Repository\Post\ExportRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\Core\Usecase\Concerns\FilterRecords;
use Log;

class Export implements Usecase
{
    use UserContext;

    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait;

    // - FilterRecords for setting search parameters
    use FilterRecords;
    protected $filters;
    private $postExportRepository;
    private $exportJobRepository;
    private $formAttributeRepository;
    private $hxlFromAttributeHxlAttributeTagRepo;

    /**
     * @var SearchRepository
     */
    protected $repo;

    /**
     * Inject a repository that can search for entities.
     *
     * @param  SearchRepository $repo
     * @return $this
     */
    public function setRepository(SearchRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    /**
     * @var SearchData
     */
    protected $search;

    // - VerifyParentLoaded for checking that the parent exists
    use VerifyParentLoaded;

    public function setExportJobRepository(ExportJobRepository $repo)
    {
        $this->exportJobRepository = $repo;//service('repository.export_job');
    }

    public function setHXLFromAttributeHxlAttributeTagRepo(HXLFormAttributeHXLAttributeTagRepository $repo)
    {
        $this->hxlFromAttributeHxlAttributeTagRepo = $repo;//service('repository.form_attribute_hxl_attribute_tag');
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
     * @return array|mixed|\Ushahidi\Core\Array
     */
    public function interact()
    {
        Log::debug('EXPORTER: on interact: ');

        // Load the export job
        $job = $this->exportJobRepository->get($this->getIdentifier('job_id'));
        // load the user from the job into the 'session'
        $this->session->setUser($job->user_id);
        Log::debug('EXPORTER: on interact - user id: ' . $job->user_id);
        // verify the user can export posts
        $this->verifyAuth($job, 'export');
        // merge filters from the controller/cli call with the job's saved filters
        $data = $this->constructSearchData($job);
        $this->postExportRepository->setSearchParams($data);

        // get the form attributes for the export
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
        Log::debug('EXPORTER: on interact Count posts: ' . count($posts));

        /**
         * update the header attributes
         * in the job table so we know which headers to
         * use in other chunks of the export
         */
        $this->saveHeaderRow($job, $attributes);

        /**
         * set 'add header' in the formatter
         * so it knows how to return the results
         * for the csv (with or without a header row)
         */
        $this->formatter->setAddHeader($this->filters['add_header']);
        // handle hxl
        $hxl_rows = $this->formatter->generateHXLRows(
            $this->formatter->createHeading($attributes),
            $this->getHxlRows($job)
        );
        $this->saveHXLHeaderRow($job, $hxl_rows);
        $this->formatter->setHxlHeading($hxl_rows);
        $formatter = $this->formatter;
        Log::debug('EXPORTER: Count posts: ' . count($posts));
        /**
         * KeyAttributes is sent instead of the header row because it contains
         * the attributes with the corresponding features (type, priority) that
         * we need for manipulating the data
         */
        $file = $formatter($posts, $job, $keyAttributes);
        return [
            'results' => [
                [
                    'file' => $file->file,
                ]
            ]
        ];
    }

    /**
     * @param $job
     * If the include_hxl flag is true, generate the heading row and include
     * the hxl heading in the csv
     */
    private function getHxlRows($job)
    {
        $hxl = [];
        if ($job->include_hxl === true) {
            $hxl = $this->hxlFromAttributeHxlAttributeTagRepo->getHxlWithFormAttributes($job);
        }
        return $hxl;
    }

    /**
     * @param $job
     * @param $attributes
     */
    private function saveHeaderRow($job, $attributes)
    {
        if (empty($job->header_row)) {
            $job->setState(['header_row' => $attributes]);
            $this->exportJobRepository->update($job);
        }
    }

    /**
     * @param $job
     * @param $hxl heading row
     */
    private function saveHXLHeaderRow($job, $hxl)
    {
        if (empty($job->hxl_heading_row)) {
            $job->setState(['hxl_heading_row' => $hxl]);
            $this->exportJobRepository->update($job);
        }
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
    public function constructSearchData($job)
    {
        $form_ids_by_attributes = $this->formAttributeRepository->getFormsByAttributes($job->fields);
        $filters = $this->constructFilters($this->filters, $job->filters);

        if (!empty($form_ids_by_attributes)) {
            $filters['form'] = array_unique(
                array_merge(
                    isset($filters['form']) ? $filters['form'] : [],
                    array_map(
                        function ($item) {
                            return intval($item);
                        },
                        $form_ids_by_attributes
                    )
                )
            );
        }

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

    /**
     * Will this usecase write any data?
     *
     * @return Boolean
     */
    public function isWrite()
    {
        return false;
    }

    /**
     * Will this usecase search for data?
     *
     * @return Boolean
     */
    public function isSearch()
    {
        return true;
    }

    /**
     * @param SearchData $search
     */
    public function setData(SearchData $search)
    {
        $this->search = $search;
    }
}
