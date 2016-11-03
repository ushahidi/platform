<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Video Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2016 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Post_Video extends Ushahidi_Validator_Post_ValueValidator
{
	protected function validate($value)
	{
		if (!Valid::url($value)) {
			return 'url';
		}
	}
}
