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

class Ushahidi_Validator_Message_Update extends Validator
{
	protected $repo;
	protected $default_error_source = 'message';

	public function __construct(UpdateMessageRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function getRules()
	{
		return [
			'status' => [
				[[$this->repo, 'checkStatus'], [':value', $this->get('direction')]]
			]
		];
	}
}
