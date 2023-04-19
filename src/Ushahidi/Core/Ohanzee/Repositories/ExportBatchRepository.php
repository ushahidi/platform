<?php

/**
 * Ushahidi Export Batch Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Repositories;

use Illuminate\Support\Collection;
use Ushahidi\Core\Ohanzee\Entities\ExportBatch;
use Ushahidi\Core\Entity\ExportBatchRepository as ExportBatchRepositoryContract;

class ExportBatchRepository extends OhanzeeRepository implements ExportBatchRepositoryContract
{
    /**
     * Get the entity for this repository.
     * @param  array  $data
     * @return \Ushahidi\Contracts\Entity
     */
    public function getEntity(array $data = null)
    {
        return new ExportBatch($data);
    }

    /**
     * Get the table name for this repository.
     * @return string
     */
    protected function getTable()
    {
        return 'export_batches';
    }

    /**
     * Get all batches for job id
     * @param  int $jobId
     * @param  string $status
     * @return array|\Illuminate\Support\Collection
     */
    public function getByJobId($jobId, $status = ExportBatch::STATUS_COMPLETED)
    {
        $results = $this
            ->selectQuery([
                'export_job_id' => $jobId,
                'status' => $status
            ])
            ->execute($this->db());

        return $this->getCollection($results->as_array());
    }

    /**
     * Get fields that can be used for searches.
     * @return array
     */
    public function getSearchFields()
    {
        return [];
    }

    /**
     * Converts an array/collection of results into an collection
     * of entities, indexed by the entity id.
     *
     *
     * @param array|\iterable $results
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCollection($results)
    {
        return Collection::wrap($results)->mapWithKeys(function ($item, $key) {
            $entity = $this->getEntity((array) $item);
            return [$entity->getId() => $entity];
        });
    }
}
