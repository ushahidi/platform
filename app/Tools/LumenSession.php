<?php

/**
 * Ushahidi Lumen Session
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Tools;

use Ushahidi\Core\Session;

class LumenSession implements Session
{
    protected $userRepo;
    protected $overrideUserId;
    protected $cachedUser;

    public function __construct($userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function setUser($userId)
    {
        // Wipe cached used
        if ($this->cachedUser) {
            unset($this->cachedUser);
        }

        // Override user id
        $this->overrideUserId = $userId;
    }

    public function getUser()
    {
        // If we haven't already loaded the user  go get it
        if (!$this->cachedUser) {
            // If user override is set
            if ($this->overrideUserId) {
                // Use that
                $userId = $this->overrideUserId;
            } else {
                // Using the OAuth resource server, get the userid (owner id) for this request
                $genericUser = app('auth')->guard()->user();
                $userId = $genericUser ? $genericUser->id : null;
            }

            // Using the user repository, load the user
            $this->cachedUser = $this->userRepo->get($userId);
        }

        return $this->cachedUser;
    }
}
