<?php

/**
 * Ushahidi Webhook Job Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Webhook;

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\WebhookJob;
use Ushahidi\Core\Entity\WebhookJobRepository as WebhookJobRepositoryContract;
use Ushahidi\App\Repository\OhanzeeRepository;

class JobRepository extends OhanzeeRepository implements WebhookJobRepositoryContract
{
    protected function getTable()
    {
        return 'webhook_job';
    }

    // OhanzeeRepository
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        foreach ([
            'post',
            'webhook',
        ] as $fk) {
            if ($search->$fk) {
                $query->where("webhook_job.{$fk}_id", '=', $search->$fk);
            }
        }
    }

    public function getEntity(array $data = null)
    {
        return new WebhookJob($data);
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $state = [
            'created' => time(),
        ];

        return parent::create($entity->setState($state));
    }

    // WebhookJobRepository
    public function getJobs($limit)
    {
        $query = $this->selectQuery()
                      ->limit($limit)
                      ->order_by('created', 'ASC');

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    public function getSearchFields()
    {
        return [
            'post',
            'webhook'
        ];
    }
}
