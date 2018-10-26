<?php

/**
 * Ushahidi Export Job Repository
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2018 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\ExportBatch;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository as ExportJobRepositoryContract;
use Ushahidi\Core\Usecase\Concerns\FilterRecords;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;
use Ohanzee\DB;
use Ohanzee\Database;

use Log;

class ExportJobRepository extends OhanzeeRepository implements ExportJobRepositoryContract
{
    // Use the JSON transcoder to encode properties
    use JsonTranscodeRepository;

    // - FilterRecords for setting search parameters
    use FilterRecords;
    use UserContext;
    use AdminAccess;

    /**
     * @var SearchData
     */
    protected $search;

    protected $post_repo;

    public function __construct(Database $db, PostRepository $post_repo)
    {
        parent::__construct($db);

        $this->post_repo = $post_repo;
    }

    protected function getTable()
    {
        return 'export_job';
    }

    // Ushahidi_JsonTranscodeRepository
    protected function getJsonProperties()
    {
        return ['fields', 'filters', 'header_row', 'hxl_heading_row'];
    }

    // OhanzeeRepository
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        $user = $this->getUser();


        if ($search->hxl_meta_data_id) {
            $query->where('hxl_meta_data_id', '=', $search->hxl_meta_data_id);
        }

        // get user ID so that we only ever get jobs from that user
        $search->user = $this->getUserId();

        // Keeping this to filter our legacy URLs
        // All new urls are generated on the fly instead, so their expiration=null
        if ($search->max_expiration) {
            $query->where("url_expiration", '>', intval($search->max_expiration));
            $query->or_where("url_expiration", 'IS', null);
            $query->or_where("url_expiration", '=', 0);
        }
        foreach ([
            'user'
        ] as $fk) {
            if ($search->$fk) {
                $query->where("export_job.{$fk}_id", '=', $search->$fk);
            }
        }

        foreach ([
            'entity_type',
        ] as $key) {
            if ($search->$key) {
                $query->where($key, '=', $search->$key);
            }
        }
    }

    public function getEntity(array $data = null)
    {
        return new ExportJob($data);
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $state = [
            'created' => time(),
            'status' => ExportJob::STATUS_PENDING,
            'user_id' => $entity->user_id,
            // Don't save this now, we need to generate it properly
            'hxl_heading_row' => null
        ];

        return parent::create($entity->setState($state));
    }

    // Overriding the update method here to handle state transitions
    public function update(Entity $entity)
    {
        // Run state transition handler
        $entity->handleStateTransition();

        return parent::update($entity);
    }

    public function getPendingJobs($limit = 10)
    {
        $query = $this->selectQuery()
                      ->limit($limit)
                      ->where('status', '=', ExportJob::STATUS_PENDING);

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    public function getJobs($limit)
    {
        $query = $this->selectQuery()
                      ->limit($limit)
                      ->order_by('created', 'ASC');

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    public function getPostCount($job_id)
    {
        $job = $this->get($job_id);

        if ($job->filters) {
            $this->setFilters($job->filters);
        }

        $fields = $this->post_repo->getSearchFields();

        $this->search = new SearchData(
            $this->getFilters($fields)
        );

        $this->search->group_by === 'form';

        $total = $this->post_repo->getGroupedTotals($this->search);

        return $total;
    }

    public function getSearchFields()
    {
        return [
            'entity_type', 'user', 'max_expiration'
        ];
    }

    /**
     * Check if job's batches are finished?
     *
     * @param  Int  $jobId
     * @return boolean
     */
    public function areBatchesFinished($jobId)
    {
        $query = $this->selectQuery([
                'export_job.id' => $jobId,
                'export_batches.status' => ExportBatch::STATUS_COMPLETED
            ])
            ->resetSelect()
            ->select([DB::expr('COUNT(DISTINCT export_batches.id)'), 'completed_batches'], 'total_batches')
            ->join('export_batches')
                ->on('export_job_id', '=', 'export_job.id')
            ->group_by(['export_job.id', 'total_batches']);

        $result = $query->execute($this->db)->current();

        return ($result['completed_batches'] == $result['total_batches']);
    }
}
