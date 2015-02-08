<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;

class Ushahidi_Validator_Set_Create extends Validator
{
	protected $user_repo;
	protected $default_error_source = 'set';

	public function __construct(UserRepository $repo)
	{
		$this->user_repo = $repo;
	}

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
			'user_id' => [
				['numeric'],
				[[$this->user_repo, 'doesUserExist'], [':value']],
			],
			'name' => [
				['not_empty'],
				['min_length', [':value', 3]],
				['max_length', [':value', 255]],
			]
		];
	}
}
