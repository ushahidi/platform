<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Message Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Usecase\Message\CreateMessageRepository;
use Ushahidi\Core\Usecase\Message\UpdateMessageRepository;
use Ushahidi\Core\Usecase\Message\DeleteMessageRepository;
use Ushahidi\Core\Usecase\Message\MessageData;

class Ushahidi_Repository_Message extends Ushahidi_Repository implements
	MessageRepository,
	UpdateMessageRepository,
	CreateMessageRepository
{
	// Use the JSON transcoder to encode properties
	use Ushahidi_JsonTranscodeRepository;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'messages';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Message($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return [
			'box', 'status', 'contact', 'parent', 'post', 'type', 'data_provider',
			'q' /* LIKE contact, title, message */
		];
	}

	// Ushahidi_JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['additional_data'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query
			->join('contacts')
				->on('contact_id', '=', 'contacts.id');

		if ($search->box === 'outbox')
		{
			// Outbox only shows outgoing messages
			$query->where('direction', '=', 'outgoing');
		}
		elseif ($search->box === 'inbox')
		{
			// Inbox only shows incoming messages
			$query->where('direction', '=', 'incoming');
		}

		// Get the requested status, which is secondary to box
		$status = $search->status;

		if ($search->box === 'archived')
		{
			// Archive only shows archived messages
			$query->where('status', '=', 'archived');
		}
		else if ($status)
		{
			if ($status !== 'all') {
				// Search for a specific status
				$query->where('status', '=', $status);
			}
		}
		else
		{
			// Other boxes do not display archived
			$query->where('status', '!=', 'archived');
		}

		if ($search->q)
		{
			$query->and_where_open();
			$query->where('contacts.contact', 'LIKE', "%$search->q%");
			$query->or_where('title', 'LIKE', "%$search->q%");
			$query->or_where('message', 'LIKE', "%$search->q%");
			$query->and_where_close();
		}

		foreach ([
			'contact',
			'parent',
			'post',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("messages.{$fk}_id", '=', $search->$fk);
			}
		}

		foreach ([
			'type',
			'data_provider',
		] as $key)
		{
			if ($search->$key)
			{
				$query->where("messages.{$key}", '=', $search->$key);
			}
		}
	}

	// MessageRepository
	public function getPendingMessages($status, $data_provider, $limit)
	{
		$direction = Message::OUTGOING;
		$query = $this->selectQuery(compact('status', 'direction', 'data_provider'))
			->limit($limit)
			->order_by('created', 'ASC');

		// @todo load contact.contact in the same query
		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	public function getTotalMessagesFromContact($contact_id)
	{
		$direction = Message::INCOMING;
		return (int) $this->selectCount(compact('contact_id', 'direction'));
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		return parent::create($entity->setState([
			// New messages cannot have any other state
			'status'    => \Message_Status::PENDING,
			'direction' => \Message_Direction::OUTGOING,
			'created'   => time(),
		]));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$state = [
			'updated'  => time(),
		];

		return parent::update($entity->setState($state));
	}

	// UpdateMessageRepository
	public function checkStatus($status, $direction)
	{
		if ($direction === \Message_Direction::INCOMING)
		{

			return ($status == \Message_Status::RECEIVED);
		}

		if ($direction === \Message_Direction::OUTGOING)
		{
			// Outgoing messages can only be: pending, cancelled, failed, unknown, sent
			return in_array($status, [
				\Message_Status::PENDING,
				\Message_Status::EXPIRED,
				\Message_Status::CANCELLED,
			]);
		}

		return false;
	}

	/**
	 * For CreateMessageRepository
	 * @param  int $parent_id
	 * @return Boolean
	 */
	public function parentExists($parent_id)
	{
		return $this->selectCount(['id' => $parent_id]) > 0;
	}

	//MessageRepository
	public function notificationMessageExists($post_id, $contact_id)
	{
		return $this->selectCount(['notification_post_id' => $post_id, 'contact_id' => $contact_id, 'direction' => 'outgoing']) > 0;
	}
}
