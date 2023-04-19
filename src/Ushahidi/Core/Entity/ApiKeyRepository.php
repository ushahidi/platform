<?php

/**
 * Repository for API Keys
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityExists;
use Ushahidi\Contracts\Repository\CreateRepository;

interface ApiKeyRepository extends CreateRepository, EntityExists
{
    /**
     * Check if an api key exists
     *
     * @param  string  $api_key
     * @return boolean
     */
    public function apiKeyExists($api_key);
}
