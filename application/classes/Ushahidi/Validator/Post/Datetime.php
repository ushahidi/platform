<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Date Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// use Ushahidi\Data;

// use Ushahidi\Tool\Validator;

class Ushahidi_Validator_Post_Datetime /*implements Validator*/
{
	public function check($input)
	{
		$this->valid = Validation::factory($input)
			->rules('value', array(
					array('date'),
				));

		return $this->valid->check();
	}

	public function errors($from = 'post')
	{
		return $this->valid->errors($from);
	}
}
