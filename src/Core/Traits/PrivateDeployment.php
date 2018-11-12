<?php

/**
 * Ushahidi Private Deployment Trait
 *
 * Gives methods to check if deployment is private
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;
use Ushahidi\App\Facades\Features;

trait PrivateDeployment
{
    /**
     * Check if the deployment is private
     * @return boolean
     */
    public function isPrivate()
    {
        $site = service('site.config');
        // if feature enabled and site set private in config
        if (Features::isEnabled('private') && $site['private']) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can access deployment
     * @return boolean
     */
    public function canAccessDeployment(User $user)
    {
        // Only logged in users have access if the deployment is private
        if ($this->isPrivate() and !$user->id) {
            return false;
        }

        return true;
    }
}
