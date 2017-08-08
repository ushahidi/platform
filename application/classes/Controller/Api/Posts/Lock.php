<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Groups Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Posts_Lock extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::POST    => 'post',
		Http_Request::PUT     => 'put',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'posts';
	}

	protected function _resource()
	{
		return 'posts_lock';
	}

	// Check Lock
	public function action_get_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'checkLock');
		$this->_usecase			
			->setIdentifiers($this->request->param())
			->setFormatter(service("formatter.entity.post.check.lock"));
	}

	// Get Lock
	public function action_post_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'getLock');
		$this->_usecase
			->setIdentifiers($this->request->param())
			->setFormatter(service("formatter.entity.post.get.lock"));
	}

	// Break Lock
	public function action_put_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'breakLock');
		$this->_usecase
			->setIdentifiers($this->request->param())
			->setFormatter(service("formatter.entity.post.break.lock"));
	}
}
