<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;

class Ushahidi_Validator_Form_Create extends Ushahidi_Validator_Form_Update
{
	public function check(Entity $input)
	{
		parent::check($input);

		$this->valid
			->rules('name', [
				['not_empty']
			]);

		return $this->valid->check();
	}

	public function errors($from = 'form')
	{
		return $this->valid->errors($from);
	}
}
