<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\Tag\DeleteTagRepository;

class Ushahidi_Validator_Tag_Delete implements Validator
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

	public function errors($from = 'tag')
	{
		return $this->valid->errors($from);
	}
}

