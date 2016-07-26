<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;

class Ushahidi_Validator_Form_Role_Create extends Ushahidi_Validator_Form_Role_Update
{
	protected $default_error_source = 'form_role';

	protected function getRules()
	{
		return [
			'form_id' => [
				['not_empty'],
			],
			'role_id' => [
				['not_empty'],
			],
		];
	}
}
