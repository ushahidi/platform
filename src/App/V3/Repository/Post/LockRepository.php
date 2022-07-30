<?php

/**
 * Ushahidi Post Lock Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Repository\Post;

use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Core\Entity\PostLock;
use League\Event\ListenerInterface;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\App\V3\Repository\OhanzeeRepository;
use Ushahidi\Contracts\Repository\Entity\PostLockRepository;
use Ushahidi\App\V3\Repository\UserRepository;
use Ushahidi\App\Multisite\OhanzeeResolver;

class LockRepository extends OhanzeeRepository implements PostLockRepository
{
    // Provides getUser()
    use UserContext;

    // Use Event trait to trigger events
    use Event;

    public function __construct(
        OhanzeeResolver $resolver
    ) {
        parent::__construct($resolver);
        $this->user_repo = new UserRepository($resolver);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_locks';
    }

    // OhanzeeRepository
    public function getSearchFields()
    {
        return [
            'post_id',
            'user_id'
        ];
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {

        return new PostLock($data);
    }

    public function releaseLock($post_id)
    {
        $result = DB::select()->from('post_locks')
            ->where('post_id', '=', $post_id)
            ->limit(1)
            ->execute($this->db());

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
            ->execute($this->db());

        $locks = $this->getCollection($results->as_array());

        foreach ($locks as $lock) {
            $this->warnUserLockBroken($lock->user_id);

            $this->delete($lock);
        }

        return;
    }

    public function warnUserLockBroken($user_id)
    {
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
            ->execute($this->db());

        if ($result->get('expires')) {
            $expire_time = $result->get('expires');
            $curtime = time();
            // Check if the lock has expired
            // Locks are active for a maximum of 5 minutes
            if (($curtime - $expire_time) > 0) {
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
    private function createNewLock($post_id)
    {
        $expires = strtotime("+5 minutes");
         $user = $this->getUser();
         $lock = [
             'user_id' => $user->id,
             'post_id' => $post_id,
             'expires' => $expires
         ];

         $query = DB::insert('post_locks')
             ->columns(array_keys($lock))
             ->values(array_values($lock));

         list($id) = $query->execute($this->db());
         return $id;
    }

    public function getLock(Entity $entity)
    {
        // if user can break the lock "has admin role" then release the old lock if it from other user
        // If the lock is inactive simply create a new
        // lock
        // If the user already owns a lock that is active
        // return that lock id
        // Otherwise we return null
        if ($this->lockIsBreakable()) {
        }if (!$this->isActive($entity->id)) {
            return $this->getAdminLock($entity->id);
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
            ->execute($this->db())
            ->as_array();

        return count($result) > 0 ? $result[0] : null;
    }

    public function getNoneExpiredPostLock($entity_id)
    {
        $result = DB::select('id', 'post_id', 'user_id', 'expires')
            ->from('post_locks')
            ->where('post_id', '=', $entity_id)
            ->where('expires', '>=', time())
            ->limit(1)
            ->execute($this->db())
            ->as_array();

        if (count($result) > 0) {
            $owner =    $this->user_repo->selectOne(['id'=>$result[0]['user_id']]);
            $result[0]["breakable"] = $this->lockIsBreakable();
            $result[0]["owner_name"] = $owner["realname"];

            return $result[0];
        } else {
            return null;
        }
    }

    public function lockIsBreakable()
    {
        $user =$this->getUser();
        return $user->role ===  "admin";
    }

    public function getAdminLock($post_id)
    {
        $result = DB::select('id', 'user_id')
        ->from('post_locks')
        ->where('post_id', '=', $post_id)
        ->limit(1)
        ->execute($this->db());
        $user = $this->getUser();

        if ($result->get('user_id') || ($result->get('user_id') != $user->id)) {
            $this->releaseLock($post_id);
            return $this->createNewLock($post_id);
        } else {
            return $result->get('id');
        }
    }
}
