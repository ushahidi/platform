<?php

/**
 * Ushahidi Privilege Access Trait
 *
 * Gives objects methods determining what privileges a entity has.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Core\Contracts\Entity;

trait PrivAccess
{
    /**
     * Get a list of all possible privilges.
     * By default, returns standard HTTP REST methods.
     * @return array
     */
    protected function getAllPrivs()
    {
        return ['read', 'create', 'update', 'delete', 'search'];
    }

    // Authorizer
    public function getAllowedPrivs(Entity $entity)
    {
        $privs = $this->getAllPrivs();
        $allowed = [];

        foreach ($privs as $priv) {
            if ($this->isAllowed($entity, $priv)) {
                $allowed[] = $priv;
            }
        }

        return $allowed;
    }

    // Authorizer
    abstract public function isAllowed(Entity $entity, $privilege);
}
