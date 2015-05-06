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
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Message\CreateMessageRepository;

class Ushahidi_Validator_Message_Receive extends Validator
{
	protected $repo;
	protected $default_error_source = 'message';

	public function __construct(CreateMessageRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function getRules()
	{
		return [
			'direction' => [
				['not_empty'],
				['in_array', [':value', [\Message_Direction::INCOMING]]],
			],
			'message' => [
				['not_empty'],
			],
			'datetime' => [
				['date'],
			],
			'type' => [
				['not_empty'],
				['max_length', [':value', 255]],
				// @todo this should be shared via repo or other means
				['in_array', [':value', ['sms', 'ivr', 'email', 'twitter']]],
			],
			'data_provider' => [
				// @todo DataProvider should provide a list of available types
				['in_array', [':value', array_keys(\DataProvider::get_providers())]],
			],
			'data_provider_message_id' => [
				['max_length', [':value', 511]],
			],
			'status' => [
				['not_empty'],
				['in_array', [':value', [
					\Message_Status::RECEIVED,
				]]],
			],
			'parent_id' => [
				['numeric'],
				[[$this->repo, 'parentExists'], [':value']],
			],
			'post_id' => [
				['numeric'],
			],
			'contact_id' => [
				['numeric'],
			]
		];
	}
}
