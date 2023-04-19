<?php

/**
 * Repository for webhooks jobs
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;
use Ushahidi\Contracts\Repository\CreateRepository;

interface WebhookJobRepository extends
    EntityGet,
    EntityExists,
    CreateRepository
{
    /**
     * Get new webhooks
     *
     * @param  int $limit
     * @return array
     */
    public function getJobs($limit);
}
