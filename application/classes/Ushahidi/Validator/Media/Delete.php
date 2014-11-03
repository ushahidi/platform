<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Media_Delete implements Validator
{
	protected $valid;

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
			->rules('id', array(
					array('not_empty'),
					array('digit'),
				));

		return $this->valid->check();
	}

	public function errors($from = 'media')
	{
		return $this->valid->errors($from);
	}
}

