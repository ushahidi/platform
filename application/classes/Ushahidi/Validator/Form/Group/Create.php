<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Group Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;

class Ushahidi_Validator_Form_Group_Create extends Ushahidi_Validator_Form_Group_Update
{
	protected $default_error_source = 'form_group';

	protected function getRules()
	{
		return [
			'form_id' => [
				['not_empty'],
			],
			'label' => [
				['not_empty'],
			],
		];
	}
}
