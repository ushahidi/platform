<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Message Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Message\UpdateMessageRepository;

class Ushahidi_Validator_Message_Update implements Validator
{
	protected $repo;
	protected $valid;

	public function __construct(UpdateMessageRepository $repo)
	{
		$this->repo = $repo;
	}

	public function check(Entity $entity)
	{
		$this->valid = Validation::factory($entity->getChanged())
			->bind(':direction', $entity->direction)
			->rules('status', [
				[[$this->repo, 'checkStatus'], [':value', ':direction']],
			]);
		return $this->valid->check();
	}

	public function errors($from = 'message')
	{
		return $this->valid->errors($from);
	}
}
