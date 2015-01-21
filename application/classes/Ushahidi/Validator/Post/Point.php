<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Point Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Post_Point extends Ushahidi_Validator_Post_ValueValidator
{
	protected function validate($value)
	{
		if (!(is_array($value) && array_key_exists('lat', $value) && array_key_exists('lon', $value))) {
			return 'point';
		}
	}
}
