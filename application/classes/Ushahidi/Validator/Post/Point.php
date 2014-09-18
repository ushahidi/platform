<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Point Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// use Ushahidi\Data;

// use Ushahidi\Tool\Validator;

class Ushahidi_Validator_Post_Point /*implements Validator*/
{
	public function check($input)
	{
		$this->valid = Validation::factory($input)
			->rules('value', array(
				// @todo better error messages
					array('is_array'),
					array('array_key_exists', array('lat', ':value')),
					array('array_key_exists', array('lon', ':value'))
				));

		return $this->valid->check();
	}

	public function errors($from = 'post')
	{
		return $this->valid->errors($from);
	}
}
