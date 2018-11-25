<?php

/**
 * Ushahidi Export Batch Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Entity\ExportBatch;
use Ushahidi\Core\Entity\ExportBatchRepository as ExportBatchRepositoryContract;

class ExportBatchRepository extends EloquentRepository implements ExportBatchRepositoryContract
{

    /**
     * Get the entity for this repository.
     * @param  Array  $data
     * @return Ushahidi\Core\Entity
     */
    public function getEntity(array $data = null)
    {
        return new ExportBatch($data);
    }

    /**
     * Get the table name for this repository.
     * @return String
     */
    protected function getTable()
    {
        return 'export_batches';
    }

    /**
     * Get all batches for job id
     * @param  int $jobId
     * @return \Illuminate\Support\Collection
     */
    public function getByJobId($jobId)
    {
        $results = $this
            ->selectQuery(['export_job_id' => $jobId])
            ->get();

        return $this->getCollection($results);
    }
}
