<?php

/**
 * Ushahidi Platform Message Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Data;

class MessageData extends Data
{
	public $id;
	public $title;
	public $message;
	public $datetime;
	public $type;
	public $data_provider;
	public $data_provider_message_id;
	public $status;
	public $direction;
	public $parent_id;
	public $post_id;
	public $contact_id;
}
