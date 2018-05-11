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

class KohanaSession implements Session
{

    protected $userRepo;
    protected $overrideUser;

    public function __construct($userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function setUser($userId)
    {
        $this->overrideUser = $userId;
    }

    public function getUser()
    {
        // If user override is set
        if ($this->overrideUser) {
            // Use that
            $userId = $this->overrideUser;
        } else {
            // Using the OAuth resource server, get the userid (owner id) for this request
            $server = service('oauth.server.resource');
            $userId = $server->getOwnerId();
        }

        // Using the user repository, load the user
        $user = $this->userRepo->get($userId);

        return $user;
    }
}
