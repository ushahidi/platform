<?php

/**
 * Ushahidi Message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Message extends Entity
{
	public $id;
	public $parent_id;
	public $contact_id;
	public $post_id;
	public $title;
	public $message;
	public $created;

	// Valid boxes are defined as constants.
	const INBOX = 'inbox';
	const OUTBOX = 'sent';
	const ARCHIVE = 'archive';

	public $box;

	// Valid directions are defined as constants.
	const INCOMING = 'incoming';
	const OUTGOING = 'outgoing';

	public $direction;

	// Valid status types are defined as constants.
	const PENDING = 'pending';
	const RECEIVED = 'received';
	const EXPIRED = 'expired';
	const CANCELLED = 'cancelled';
	const FAILED = 'failed';

	public $status;

	public function getResource()
	{
		return 'messages';
	}
}
