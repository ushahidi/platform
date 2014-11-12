<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Tag_Create extends Ushahidi_Validator_Tag_Update
{
	public function check(Data $input)
	{
		parent::check($input);

		// Has the same requirements as update validation, but also requires
		// some fields to be defined.
		$this->valid
			->rules('tag', array(
					array('not_empty'),
				))
			->rules('slug', array(
					array('not_empty'),
				))
			->rules('type', array(
					array('not_empty'),
				))
			->rules('role', array(
				array([$this->role, 'doRolesExist'], array(':value')),
				));

		return $this->valid->check();
	}
}
