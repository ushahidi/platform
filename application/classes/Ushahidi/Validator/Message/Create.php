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

class Ushahidi_Validator_Message_Create implements Validator
{
	protected $repo;
	protected $valid;

	public function __construct(CreateMessageRepository $repo)
	{
		$this->repo = $repo;
	}

	public function check(Entity $entity)
	{
		// Users can only create outgoing messages.
		$this->valid = Validation::factory($entity->asArray())
			->rules('direction', [
					['not_empty'],
					['in_array', [':value', [\Message_Direction::OUTGOING]]],
				])
			->rules('message', [
					['not_empty']
			])
			->rules('datetime',[
					['date'],
			])
			->rules('type', [
					['not_empty'],
					['max_length', [':value', 255]],
					// @todo this should be shared via repo or other means
					['in_array', [':value', ['sms', 'ivr', 'email', 'twitter']]],
			])
			->rules('data_provider', [
					// @todo DataProvider should provide a list of available types
					['in_array', [':value', array_keys(\DataProvider::get_providers())]],
			])
			->rules('data_provider_message_id', [
					['max_length', [':value', 511]],
			])
			->rules('parent_id', [
					['numeric'],
					[[$this->repo, 'parentExists'], [':value']]
			])
			->rules('post_id', [
					['numeric'],
			])
			->rules('contact_id', [
					['numeric'],
			]);

		return $this->valid->check();
	}

	public function errors($from = 'message')
	{
		return $this->valid->errors($from);
	}
}
