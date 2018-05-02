<?php

/**
 * Ushahidi HXL Access Trait
 *
 * Gives method to check if the deployment can use HXL features
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait HXLFeatureAccess
{
	protected $enabled = false;

	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * Check if the deployment can use the HXL feature
	 * @return boolean
	 */
	public function canUseHXL()
	{
		return (bool) $this->enabled;
	}
}
