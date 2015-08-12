<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Tags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_PasswordReset extends Ushahidi_Rest {

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
			->get($this->_scope(), 'getresettoken')
			->setPayload($this->_payload());
	}

	public function action_options_confirm_collection()
	{
		$this->action_options_index_collection();
	}

	public function action_post_confirm_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_scope(), 'passwordreset')
			->setPayload($this->_payload());
	}
}
