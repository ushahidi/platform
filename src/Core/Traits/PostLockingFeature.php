<?php

/**
 * Ushahidi Post Locking Access Trait
 *
 * Gives method to check if user can lock posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait PostLockingFeature
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
	public function isPostLockingEnabled()
	{
		return (bool) $this->enabled;
	}
}
