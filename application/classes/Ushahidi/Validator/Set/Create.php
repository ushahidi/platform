<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\UserRepository;

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Set_Create implements Validator
{
	protected $user_repo;
	protected $valid;

	public function __construct(UserRepository $repo)
	{
		$this->user_repo = $repo;
	}

	public function check(Entity $entity)
	{
		$this->valid = Validation::factory($entity->asArray())
			->rules('id',[
					['numeric']
			])
			->rules('user_id',[
					['numeric'],
					[[$this->user_repo, 'doesUserExist'], [':value']]
			])
			->rules('name',[
					['not_empty'],
					['min_length', [':value', 3]],
					['max_length', [':value', 255]]
			]);

			return $this->valid->check();
	}

	public function errors($from = 'set')
	{
		return $this->valid->errors($from);
	}
}
