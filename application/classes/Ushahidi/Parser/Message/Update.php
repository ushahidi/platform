<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update Message Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Message\MessageData;

class Ushahidi_Parser_Message_Update implements Parser
{
	public function __invoke(Array $data)
	{
		$message = new MessageData($data);
		return $message;
	}
}

