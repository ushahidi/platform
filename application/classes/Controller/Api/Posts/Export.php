<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Exports Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_Export extends Ushahidi_Rest
{
	protected $_action_map = array
	(
		Http_Request::GET => 'get',
		Http_Request::OPTIONS => 'options',
	);

	protected function _scope()
	{
		return 'posts';
	}

	protected function _resource()
	{
		return 'posts_export';
	}

	public function action_get_index_collection()
	{
		// Get usecase with default formatter
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'export')
			->setFilters($this->_filters());

		// ...or use a different one if requested
		$format = strtolower($this->request->query('format'));

		if ($format) {
			$this->_usecase->setFormatter(service("formatter.entity.post.$format"));
		}
	}

	public function action_get_index()
	{
		throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
			':method'          => $this->request->method(),
			':allowed_methods' => implode(', ', array_keys($this->_action_map)),
		))
		->allowed(array_keys($this->_action_map));
	}
}
