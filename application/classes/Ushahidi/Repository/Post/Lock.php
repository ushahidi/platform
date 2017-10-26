<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Lock Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostLock;
use Ushahidi\Core\Entity\PostLockRepository;
use Ushahidi\Core\Traits\UserContext;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;

class Ushahidi_Repository_Post_Lock extends Ushahidi_Repository implements PostLockRepository
{
	// Provides getUser()
	use UserContext;

	// Use Event trait to trigger events
	use Event;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_locks';
	}

	// Ushahidi_Repository
	public function getSearchFields()
	{
		return [
			'post_id',
			'user_id'
		];
	}

    // Ushahidi_Repository
	public function getEntity(Array $data = null)
	{

		return new PostLock($data);
	}

	public function releaseLock($post_id)
	{
		$result = DB::select()->from('post_locks')
			->where('post_id', '=', $post_id)
			->limit(1)
			->execute($this->db);

		$this->warnUserLockBroken($result->get('user_id'));

		$lock = $this->get($result->get('id'));

		$this->executeDelete(['id' => $result->get('id')]);

		return $lock;
	}

	public function releaseLockByLockId($lock_id)
	{

		$lock = $this->get($lock_id);

		$this->warnUserLockBroken($lock->user_id);

		$this->delete($lock);

		return $lock;
	}

	public function releaseLocksByUserId($user_id)
	{
		$results = DB::select()->from('post_locks')
			->where('user_id', '=', $user_id)
			->execute($this->db);

		$locks = $this->getCollection($results->as_array());

		foreach($locks as $lock) {
			$this->warnUserLockBroken($lock->user_id);

			$this->delete($lock);
		}

		return;
	}

	public function warnUserLockBroken($user_id) {
		$user = $this->getUser();

		if ($user_id !== $user->id) {
			$this->emit($this->event, $user_id);
		}

		return;
	}

	public function isActive($post_id)
	{
		$result = DB::select('expires')
			->from('post_locks')
			->where('post_id', '=', $post_id)
			->limit(1)
			->execute($this->db);

		if ($result->get('expires'))
		{
			$time = $result->get('expires');
			$curtime = time();
			// Check if the lock has expired
			// Locks are active for a maximum of 5 minutes
			if(($curtime - $time) > 300)
			{
				$release = $this->releaseLock($post_id);
				return false;
			}
			return true;
		}
		return false;
	}

	public function postIsLocked($post_id)
	{
		$user = $this->getUser();
		$lock = $this->getPostLock($post_id);

		if (empty($lock)) {
			return false;
		} elseif ($user->id === (int)$lock['user_id']) {
			return false;
		} elseif (!$this->isActive($post_id)) {
			return false;
		}

		return true;
	}

	public function getLock(Entity $entity)
	{
		// If the lock is inactive simply create a new
		// lock
		// If the user already owns a lock that is active
		// return that lock id
		// Otherwise we return null

		if (!$this->isActive($entity->id))
		{
			$expires = strtotime("+5 minutes");
			$user = $this->getUser();
			$lock = [
				'user_id' => $user->id,
				'post_id' => $entity->id,
				'expires' => $expires
			];

			$query = DB::insert('post_locks')
				->columns(array_keys($lock))
				->values(array_values($lock));

			list($id) = $query->execute($this->db);

			return $id;
		} elseif ($this->userOwnsLock($entity->id)) {
			$lock = $this->getPostLock($entity->id);
			return $lock['id'];
		}
		return null;
	}

	// TODO: Most of these functions can besimplified with a proper ORM
	public function userOwnsLock($post_id)
	{
		$user = $this->getUser();
		$lock = $this->getPostLock($post_id);
		return intval($user->id) === intval($lock['user_id']);
	}

	public function getPostLock($entity_id)
	{
		$result = DB::select('id', 'post_id', 'user_id', 'expires')
			->from('post_locks')
			->where('post_id', '=', $entity_id)
			->limit(1)
			->execute($this->db)
			->as_array();

		return count($result) > 0 ? $result[0] : NULL;
	}

}
