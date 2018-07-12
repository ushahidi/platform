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
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository as ExportJobRepositoryContract;
use Ushahidi\Core\Usecase\Concerns\FilterRecords;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\App\Events\SendToHDXEvent;
use Ohanzee\DB;
use Ohanzee\Database;

use Event;
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
            'status' => "pending",
            'user_id' => $entity->user_id,
            'hxl_heading_row' => null
        ];

        return parent::create($entity->setState($state));
    }

    // overriding the update method here to intercept 'EXPORT_COMPLETED'
    //   and decide if we still need to send something to HDX or mark this
    //   as SUCCESSful
    public function update(Entity $entity)
    {
        //check for new status of 'EXPORTED_TO_CDN'
        if ($entity->status == 'EXPORTED_TO_CDN' && $entity->send_to_hdx == true) {
            parent::update($entity->setState(['status' => "PENDING_HDX"]));
            //if sending to HXL is required, then we spawn an event to do that
            Event::fire(new SendToHDXEvent($entity->id));
        } elseif ($entity->status == 'EXPORTED_TO_CDN' && $entity->send_to_hdx == false) {
            //if sending to HDX is not required, (or send_to_hdx does not exist)
            // then simply update the status to success
            parent::update($entity->setState([ 'status' => "SUCCESS"]));
        } else {
            return parent::update($entity);
        }
    }



    public function getPendingJobs($limit = 10)
    {
        $query = $this->selectQuery()
                      ->limit($limit)
                      ->where('status', '=', 'pending');

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
}
