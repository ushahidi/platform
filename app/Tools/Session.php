<?php

/**
 * Ushahidi Lumen Session
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace App\Tools;

use Illuminate\Support\Facades\Auth;
use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\Session as SessionContract;

class Session implements SessionContract
{
    protected $userRepo;

    protected $overrideUserId = false;

    protected $cachedUser = false;

    public function __construct(EntityGet $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Override the user set in oauth / lumen auth layer
     * with something else.
     *
     * This is primarily used to when running background jobs in a user
     * context. ie. an export that needs to run with the same permissions
     * as a user who triggered it
     *
     * @param int $userId
     */
    public function setUser(int $userId)
    {
        // Override user id
        $this->overrideUserId = $userId;
    }

    public function getUser()
    {
        // If user override is set
        if ($this->overrideUserId) {
            // Use that
            $userId = $this->overrideUserId;
        } else {
            // Using the OAuth resource server, get the userid (owner id) for this request
            $genericUser = Auth::guard()->user();
            $userId = $genericUser ? $genericUser->id : null;
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
