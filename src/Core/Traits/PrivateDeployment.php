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

trait PrivateDeployment
{
	protected $private;

	public function setPrivate($private)
	{
		$this->private = $private;
	}

	/**
	 * Check if the deployment is private
	 * @return boolean
	 */
	public function isPrivate()
	{
		return (bool) $this->private;
	}

	/**
	 * Check if user can access deployment
	 * @return boolean
	 */
	public function hasAccess()
	{
		// Only logged in users have access if the deployment is private
		if ($this->isPrivate() and !$this->getUserId()) {
			return false;
		}

		return true;
	}
}
