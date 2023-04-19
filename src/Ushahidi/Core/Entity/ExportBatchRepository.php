<?php

/**
 * Repository for export jobs
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2022 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;

interface ExportBatchRepository extends
    CreateRepository,
    UpdateRepository
{
    /**
     * Get all batches for job id
     *
     * @param  int $jobId
     * @param  string $status
     *
     * @return \Illuminate\Support\Collection
     */
    public function getByJobId($jobId, $status);
}
