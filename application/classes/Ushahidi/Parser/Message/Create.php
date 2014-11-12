<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Message Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Message\MessageData;

class Ushahidi_Parser_Message_Create implements Parser
{
	public function __invoke(Array $data)
	{
		// unpack user to get contact_id
		if (isset($data['contact']))
		{
			if (is_array($data['contact']) AND isset($data['contact']['id']))
			{
				$data['contact_id'] = $data['contact']['id'];
			}
			elseif (! is_array($data['contact']))
			{
				$data['contact_id'] = $data['contact'];
			}
		}

		// unpack to get parent_id
		if (isset($data['parent']))
		{
			if (is_array($data['parent']) AND isset($data['parent']['id']))
			{
				$data['parent_id'] = $data['parent']['id'];
			}
			elseif (! is_array($data['parent']))
			{
				$data['parent_id'] = $data['parent'];
			}
		}

		$data['status'] = 'pending';

		$valid = Validation::factory($data)
			->rules('message', [
				['not_empty'],
			])
			->rules('type', [
				['not_empty'],
			])
			->rules('direction', [
				['not_empty'],
			])
			->rules('status', [
				['not_empty'],
			]);

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse message create request", $valid->errors('message'));
		}

		// Ensure that all properties of a Message entity are defined by using Arr::extract
		return new MessageData(Arr::extract($data, ['title', 'message', 'datetime', 'type', 'data_provider', 'data_provider_message_id', 'status', 'direction', 'parent_id', 'post_id', 'contact_id']));
	}
}

