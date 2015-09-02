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

class Ushahidi_Validator_Notification_Update extends Validator
{
	protected $repo;
	protected $default_error_source = 'notification';

	public function __construct(Ushahidi_Repository_Notification $repo)
	{
		$this->repo = $repo;
	}

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
				[[$this->repo, 'exists'], [':value']],
			],
			'contact_id' => [
				['numeric'],
				[[$this->repo, 'exists'], [':value']],
			],
			'set_id' => [
				['numeric'],
				[[$this->repo, 'exists'], [':value']],
			],
			'is_subscribed' => [
				['numeric'],
			]
		];
	}
}
