<?php

/**
 * Ushahidi Data Export Access Trait
 *
 * Gives method to check if user can import data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait DataExportAccess
{
	protected $enabled = false;

	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * Check if the user can export data
	 * @return boolean
	 */
	public function canExportData()
	{
		return (bool) $this->enabled;
	}
}
