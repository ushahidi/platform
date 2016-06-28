<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\RoleRepository;

class Ushahidi_Validator_User_Create extends Ushahidi_Validator_User_Update
{
	protected $default_error_source = 'user';

	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
			'email' => [
				['not_empty'],
			],
			'password' => [
				['not_empty'],
			],
		]);
	}
}
