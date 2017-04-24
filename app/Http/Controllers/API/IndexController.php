<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\Controller;
use Ushahidi\App\Http\Controllers\RESTController;

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class IndexController extends Controller {

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function index()
	{
		$user = service('session.user');

		return [
			'now'       => date(\DateTime::W3C),
			'version'   => RESTController::version(),
			'user'      => [
				'id'       => $user->id,
				'email'    => $user->email,
				'realname' => $user->realname,
			],
		];
	}
}
