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

trait InteractsWithFormPermissions
{
    protected $formPermissions;

    public function setFormPermissions(FormPermissions $formPermissions)
    {
        $this->formPermissions = $formPermissions;
    }
}
