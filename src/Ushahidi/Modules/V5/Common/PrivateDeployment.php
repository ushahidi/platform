<?php
/**
 * Ushahidi Private Deployment Trait
 *
 * Gives methods to check if deployment is private
 *
 */

namespace Ushahidi\Modules\V5\Common;

use Ushahidi\Core\Facade\Features;
use Ushahidi\Multisite\UsesSiteInfo;

trait PrivateDeployment
{
    use UsesSiteInfo;

    /**
     * Check if the deployment is private
     * @return boolean
     */
    public function isPrivate()
    {
        // if feature enabled and site set private in config
        if (Features::isEnabled('private') && $this->getSite()->getSiteConfig('private', false)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can access deployment
     * @return boolean
     */
    public function canAccessDeployment($user)
    {
        // Only logged in users have access if the deployment is private
        if ($this->isPrivate() and !$user->id) {
            return false;
        }

        return true;
    }
}
