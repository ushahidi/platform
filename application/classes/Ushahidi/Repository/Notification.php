<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Notification Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
//use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Notification;
//use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\NotificationRepository;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Repository_Notification extends Ushahidi_Repository implements NotificationRepository
{
	use UserContext;

	protected $contact_repo;

	private function notificationExists($contact_id, $set_id)
	{
		$result = DB::select('id')
			->from('notifications')
			->where('contact_id', '=', $contact_id)
			->and_where('set_id', '=', $set_id)
			->execute($this->db);
		return (bool) $result->get('id', 0);
	}

	private function subscriptionStatusChanged($entity)
	{
		$result = DB::select('is_subscribed')
			->from('notifications')
			->where('id', '=', $entity->id)
			->execute($this->db);
		$is_subscribed = (int) $result->get('is_subscribed', 0);

		return $is_subscribed !== $entity->is_subscribed;
	}

	private function contactId($user_id)
	{
		$result = DB::select('id')
			->from('contacts')
			->where('user_id', '=', $user_id)
			->execute($this->db);

		return (int) $result->get('id', 0);
	}

	protected function getTable()
	{
		return 'notifications';
	}

	public function getEntity(Array $data = null)
	{
		return new Notification($data);
	}

	// NotificationRepository
	public function getNotifications($contact_id)
	{
		// Get a list of notifications for shis contact
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$contact_id = $this->contactId($this->getUserId());

		// Return a 204 since we won't be creating this subscription
		if ($this->notificationExists($contact_id, $entity->set_id)) {
			return;
		}

		$state = [
			'contact_id' => $contact_id,
			'created' => time(),
			'is_subscribed' => 1,
		];

		return parent::create($entity->setState($state));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		// Return a 204 since we won't be updating this subscription
		if (! $this->subscriptionStatusChanged($entity)) {
			return;
		}

		$state = [
			'updated'  => time(),
		];

		return parent::update($entity->setState($state));
	}

	public function getSearchFields()
	{
		return [
			'contact_id',
			'set_id',
		];
	}
}
