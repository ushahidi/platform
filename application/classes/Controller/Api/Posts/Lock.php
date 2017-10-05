<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi API Post Lock Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_API_Posts_Lock extends Ushahidi_Rest {
	protected $_action_map = array
	(
		Http_Request::PUT     => 'put',
		Http_Request::DELETE  => 'delete',
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

	// Get Lock
	public function action_put_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'create');
		$this->_usecase
			->setIdentifiers($this->request->param())
			->setFormatter(service("formatter.entity.post.lock"));
	}
	// Break Lock
	public function action_delete_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'delete');
		$this->_usecase
			->setIdentifiers($this->request->param())
			->setFormatter(service("formatter.entity.post.lock"));
	}
}
