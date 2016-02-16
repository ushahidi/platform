<?php

/**
 * Ushahidi Data Import Access Trait
 *
 * Gives method to check if user can import data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait DataImportAccess
{
	protected $enabled = false;

	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * Check if the user can import data
	 * @return boolean
	 */
	public function canImportData()
	{
		return (bool) $this->enabled;
	}
}
