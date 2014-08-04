<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Formatter_Tag extends Ushahidi_Formatter_API
{
	protected function format_color($value)
	{
		// enforce a leading hash on color, or null if unset
		$value = ltrim($value, '#');
		return $value ? '#' . $value : null;
	}

	protected function format_role($value)
	{
		if (!empty($value))
		{
			$value = json_decode($value);
			return $value;
		}
	}
}
