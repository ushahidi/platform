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
		if (!($this->checkLat($value['lat']))) {
			return 'lat';
		}
		if (!($this->checkLon($value['lon']))) {
			return 'lon';
		}
	}

	private function checkLon($lon)
	{
		if (!is_numeric($lon)) {
			return false;
		}

		if ($lon < -180 || $lon > 180) {
			return false;
		}

		return true;
	}

	private function checkLat($lat)
	{
		if (!is_numeric($lat)) {
			return false;
		}

		if ($lat < -90 || $lat > 90) {
			return false;
		}

		return true;
	}
}
