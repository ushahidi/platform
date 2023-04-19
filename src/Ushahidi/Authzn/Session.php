<?php

/**
 * Ushahidi Lumen Session
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Authzn;

use Illuminate\Support\Facades\Auth;
use Ushahidi\Contracts\Session as SessionContract;
use Ushahidi\Core\Entity\UserRepository;

class Session implements SessionContract
{
    protected $user = null;

    protected $cachedUser = null;

    protected $userRepo;

    public function __construct(UserRepository $userRepo = null)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Override the user set in oauth / framework auth layer
     * with something else.
     *
     * This is primarily used to when running background jobs in a user
     * context. ie. an export that needs to run with the same permissions
     * as a user who triggered it
     *
     * @param int $user
     */
    public function setUser(int $user)
    {
        // Override user id
        $this->user = $user;
    }

    public function setUserRepo(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUser()
    {
        // If user override is set
        if ($this->user) {
            // Use that
            $userId = $this->user;
        } else {
            // Using the OAuth resource server, get the userid (owner id) for this request
            $userId = ($genericUser = Auth::guard()->user()) ? $genericUser->id : null;
        }

        // If we have no user id return
        if (! $userId) {
            // return an empty user
            return $this->userRepo->getEntity();
        }

        // If we haven't already loaded the user, or the user has changed
        if (! $this->cachedUser || $this->cachedUser->getId() !== $userId) {
            // Using the user repository, load the user
            $this->cachedUser = $this->userRepo->get($userId);
        }

        return $this->cachedUser;
    }
}
