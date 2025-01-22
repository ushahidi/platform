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

use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Tool\SearchData;
use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Entity\ExportBatch;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Concerns\FilterRecords;
use Ushahidi\Modules\V3\Repository\ExportJobRepository;
use Ushahidi\Modules\V3\Repository\Post\ExportRepository;
use Ushahidi\Modules\V3\Repository\Form\AttributeRepository;
use Ushahidi\Modules\V3\Repository\HXL\HXLFormAttributeHXLAttributeTagRepository;
use Ushahidi\Core\Usecase\Concerns\VerifyParentLoaded;
use Ushahidi\Contracts\Repository\Entity\ExportBatchRepository;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;

class ExportPost implements Usecase
{
    use UserContext;

    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait;

    // - FilterRecords for setting search parameters
    use FilterRecords;
    // - VerifyParentLoaded for checking that the parent exists
    use VerifyParentLoaded;

    private $postExportRepository;
    private $exportJobRepository;
    private $formAttributeRepository;
    private $hxlFromAttributeHxlAttributeTagRepo;

    /**
     * @var \Ushahidi\Contracts\Repository\Entity\ExportBatchRepository
     */
    protected $repo;

    /**
     * Inject a repository to create/update ExportBatches
     *
     * @param  ExportBatchRepository $repo
     * @return $this
     */
    public function setRepository(ExportBatchRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    /**
     * @var SearchData
     */
    protected $search;

    public function setExportJobRepository(ExportJobRepository $repo)
    {
        $this->exportJobRepository = $repo;
    }

    public function setHXLFromAttributeHxlAttributeTagRepo(HXLFormAttributeHXLAttributeTagRepository $repo)
    {
        $this->hxlFromAttributeHxlAttributeTagRepo = $repo;
    }

    public function setFormAttributeRepository(AttributeRepository $repo)
    {
        $this->formAttributeRepository = $repo;
    }

    public function setPostExportRepository(ExportRepository $repo)
    {
        $this->postExportRepository = $repo;
    }

    /**
     * @return array|mixed
     */
    public function interact()
    {
        Log::debug('EXPORTER: on interact: ');

        // Load the export job
        $job = $this->exportJobRepository->get($this->getIdentifier('job_id'));
        // load the user from the job into the 'session'
        $this->session->setUser($job->user_id);
        Log::debug('EXPORTER: on interact - user id: ' . $job->user_id);

        // Create new export batch status=pending
        $batchEntity = $this->repo->getEntity()->setState([
            'export_job_id' => $job->id,
            'batch_number' => $this->getIdentifier('batch_number'),
            'status' => ExportBatch::STATUS_PENDING,
            'has_headers' => $this->getFilter('add_header', false)
        ]);
        $batchId = $this->repo->create($batchEntity);

        try {
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
        } catch (\Exception $e) {
            // Mark batch as failed
            $batchEntity = $this->repo->get($batchId);
            $batchEntity->setState([
                'status' => ExportBatch::STATUS_FAILED
            ]);
            $this->repo->update($batchEntity);
            // And rethrow the error
            throw $e;
        }

        // Update export batch status=done
        // Include filename, post count, header row etc
        $batchEntity = $this->repo->get($batchId);
        $batchEntity->setState([
            'status' => ExportBatch::STATUS_COMPLETED,
            'filename' => $file->file,
            'rows' => count($posts),
        ]);
        $this->repo->update($batchEntity);

        return [
            'filename' => $file->file,
            'id' => $batchId,
            'jobId' => $job->id,
            'rows' => $batchEntity->rows,
            'status' => $batchEntity->status
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
     * @param $hxl
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
