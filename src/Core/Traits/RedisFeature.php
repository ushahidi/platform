<?php

/**
 * Ushahidi Redis Feature Trait
 *
 * Gives method to check if user has redis feature
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait RedisFeature
{
	protected $enabled = false;

	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * Check if the user has Redis feature
	 * @return boolean
	 */
	public function isRedisEnabled()
	{
		return (bool) $this->enabled;
	}
}
