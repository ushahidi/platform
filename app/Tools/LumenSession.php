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

	public function __construct($userRepo)
	{
		$this->userRepo = $userRepo;
	}

	function getUser () {
        // Using the OAuth resource server, get the userid (owner id) for this request
        $genericUser = app('auth')->guard()->user();

        // Using the user repository, load the user
        $user = $this->userRepo->get($genericUser ? $genericUser->id : null);

        return $user;
    }

}
