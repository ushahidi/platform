<?php

/**
 * Ushahidi Post Comment Access Trait
 *
 * Gives method to check if user can comment posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait PostCommentFeature
{
    protected $enabled = false;

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Check if the user has PostLocking feature
     * @return boolean
     */
    public function isPostCommentEnabled()
    {
        return (bool) $this->enabled;
    }
}
