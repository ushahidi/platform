<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Register Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Register extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::POST    => 'post',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'users';
	}

	public function action_post_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_scope(), 'register')
			->setPayload($this->_payload());
	}
}
