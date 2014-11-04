<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Form_Update implements Validator
{
	protected $valid;

	public function check(Data $input)
	{
		$data = $input->asArray();

		if (isset($data['disabled'])) {
			$data['disabled'] = filter_var($data['disabled'], FILTER_VALIDATE_BOOLEAN);
		}

		$this->valid = Validation::factory($data)
			->rules('name', [
				['min_length', [':value', 2]],
				['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
			])
			->rules('description', [
				['is_string'],
			])
			->rules('disabled', [
				['in_array', [':value', [true, false]]]
			]);

		return $this->valid->check();
	}

	public function errors($from = 'form')
	{
		return $this->valid->errors($from);
	}
}
