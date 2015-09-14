<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Notification Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\SetRepository;

class Ushahidi_Validator_Notification_Create extends Validator
{
	protected $repo;
	protected $set_repo;
	protected $default_error_source = 'notification';

	public function __construct(UserRepository $repo, SetRepository $set_repo)
	{
		$this->repo = $repo;
		$this->set_repo = $set_repo;
	}

	protected function getRules()
	{
		return [
			'user_id' => [
				['numeric'],
				[[$this->repo, 'exists'], [':value']],
			],
			'set_id' => [
				['numeric'],
				[[$this->set_repo, 'exists'], [':value']],
			]
		];
	}
}
