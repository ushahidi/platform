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

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
			->rules('tag', array(
					array('not_empty'),
				))
			->rules('slug', array(
					array('not_empty'),
					array('alpha_dash'),
					array([$this->repo, 'isSlugAvailable'], array(':value')),
				))
			->rules('description', array(
					// alphas, numbers, punctuation, and spaces
					array('regex', array(':value', '/^[\pL\pN\pP ]++$/uD')),
				))
			->rules('type', array(
					array('not_empty'),
					array('in_array', array(':value', array('category', 'status'))),
				))
			->rules('color', array(
					array('color'),
				))
			->rules('icon', array(
					array('alpha_dash'),
				))
			->rules('priority', array(
					array('digit'),
				));

		return $this->valid->check();
	}

	public function errors($from = 'tag')
	{
		return $this->valid->errors($from);
	}
}
