<?php

/**
 * Repository for export jobs
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;
use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\UpdateRepository;

interface ExportJobRepository extends
    EntityExists,
    CreateRepository,
    ReadRepository,
    UpdateRepository
{
    /**
     * Get new webhooks
     *
     * @param  int $limit
     * @return array
     */
    public function getJobs($limit);

    /**
     * Check if job batches are finished?
     *
     * @param  Int  $jobId
     * @return boolean
     */
    public function areBatchesFinished($jobId);

    /**
     * @param  int $job_id
     * @return int
     */
    public function getPostCount($job_id);
}
