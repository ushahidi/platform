<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Validator_Contact_Create extends Ushahidi_Validator_Contact_Update
{
	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
			'type' => [
				['not_empty'],
			],
			'contact' => [
				['not_empty'],
			]
		]);
	}
}
