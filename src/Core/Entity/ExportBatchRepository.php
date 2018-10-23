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

use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Usecase\UpdateRepository;

interface ExportBatchRepository extends
    CreateRepository,
    UpdateRepository
{
    /**
     * Get all batches for job id
     * @param  int $jobId
     * @return \Illuminate\Support\Collection
     */
    public function getByJobId($jobId);
}
