<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Users extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'users';
	}

	/**
	 * Get current user
	 *
	 * GET /api/users/me
	 *
	 * @return void
	 */
	public function action_get_me()
	{
		$this->action_get_index();

		if ($id = service('session.user')->getId()) {
			// Replace the "me" id with the actual id
			$this->_usecase->setIdentifiers(['id' => $id]);
		}
	}

	/**
	 * Update current user
	 *
	 * PUT /api/users/me
	 *
	 * @return void
	 */
	public function action_put_me()
	{
		$this->action_put_index();

		if ($id = service('session.user')->getId()) {
			// Replace the "me" id with the actual id
			$this->_usecase->setIdentifiers(['id' => $id]);
		}
	}
}
