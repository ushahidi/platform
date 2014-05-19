<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Request;
use Ushahidi\Usecase\Tag\CreateTagRepository;

use Ushahidi\Tool\Validator;

class Ushahidi_Validator_Tag_Create implements Validator
{
	private $repo;
	private $valid;

	public function __construct(CreateTagRepository $repo)
	{
		$this->repo = $repo;
	}

	public function check(Array $input)
	{
		$this->valid = Validation::factory($input)
			->rules('tag', array(
					array('not_empty'),
				))
			->rules('slug', array(
					array('not_empty'),
					array('alpha_dash'),
					array([$this->repo, 'isSlugAvailable'], array(':value')),
				))
			->rules('type', array(
					array('not_empty'),
					array('in_array', array(':value', array('category', 'status'))),
				));

		return $this->valid->check();
	}

	public function errors($from = 'tag')
	{
		return $this->valid->errors($from);
	}
}

