<?php

/**
 * Ushahidi Message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Message extends StaticEntity
{
	// Valid boxes are defined as constants.
	const INBOX = 'inbox';
	const OUTBOX = 'sent';
	const ARCHIVE = 'archive';

	// Valid directions are defined as constants.
	const INCOMING = 'incoming';
	const OUTGOING = 'outgoing';

	// Valid status types are defined as constants.
	const PENDING = 'pending';
	const RECEIVED = 'received';
	const EXPIRED = 'expired';
	const CANCELLED = 'cancelled';
	const FAILED = 'failed';

	protected $id;
	protected $parent_id;
	protected $contact_id;
	protected $post_id;
	protected $data_provider;
	protected $data_provider_message_id;
	protected $title;
	protected $message;
	protected $datetime;
	protected $type;
	protected $status;
	protected $direction;
	protected $created;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'         => 'int',
			'parent_id'  => 'int',
			'contact_id' => 'int',
			'post_id'    => 'int',
			'title'      => 'string',
			'message'    => 'string',
			'datetime'   => '*timestamp',
			'type'       => 'string',
			'status'     => 'string',
			'direction'  => 'string',
			'created'    => 'int',
		] + [
			// data provider relations
			'data_provider'            => 'string',
			'data_provider_message_id' => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'messages';
	}
}
