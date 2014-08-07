<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Data;
use Ushahidi\Tool\Validator;
use Ushahidi\Usecase\Media\DeleteMediaRepository;

class Ushahidi_Validator_Media_Delete implements Validator
{
	protected $valid;

	private $repo;

	public function __construct(DeleteMediaRepository $repo)
	{
		$this->repo = $repo;
	}

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
			->rules('id', array(
					array('not_empty'),
					array('digit'),
				))
			->rules('user_id', array(
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

