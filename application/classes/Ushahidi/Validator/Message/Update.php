<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Message Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Usecase\Message\UpdateMessageRepository;
use Ushahidi\Core\Entity\RoleRepository;

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Message_Update implements Validator
{
	protected $repo;
	protected $valid;
	protected $message_direction;

	public function __construct(UpdateMessageRepository $repo)
	{
		$this->repo = $repo;
	}

	public function setDirection($direction)
	{
		$this->message_direction = $direction;
	}

	public function getDirection()
	{
		if (!$this->message_direction)
		{
			throw new \LogicException('Must call setDirection before calling getDirection for validation');
		}

		return $this->message_direction;
	}

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray())
			->rules('status', [
				[[$this->repo, 'checkStatus'], [':value', $this->getDirection()]],
			]);
		return $this->valid->check();
	}

	public function errors($from = 'message')
	{
		return $this->valid->errors($from);
	}
}
