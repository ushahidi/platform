<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_SavedSearch_Create extends Ushahidi_Validator_SavedSearch_Update
{

	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
	  'name' => [
				['not_empty'],
		],
		'filter' => [
				['not_empty'],
			]
		]);
	}
}
