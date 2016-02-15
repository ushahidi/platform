<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Permission Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Permission_Create extends Ushahidi_Validator_Permission_Update
{
	protected function getRules()
	{
		return parent::getRules() +
			[
				'name' => [
					['not_empty'],
				]
			];
	}
}
