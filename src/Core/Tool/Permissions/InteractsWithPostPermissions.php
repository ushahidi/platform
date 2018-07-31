<?php

/**
 * InteractsWithPostPermissions
 *
 * Gives objects a method for storing a post permissions
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Permissions;

trait InteractsWithPostPermissions
{
    protected $postPermissions;

    public function setPostPermissions(PostPermissions $postPermissions)
    {
        $this->postPermissions = $postPermissions;
    }
}
