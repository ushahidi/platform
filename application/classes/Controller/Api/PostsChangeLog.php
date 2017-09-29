<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase;

class Controller_Api_PostsChangeLog extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'postschangelog';
	}

	protected function _resource()
	{
		return 'postschangelog';
	}

	/**
	 * Retrieve All Post Log Entries
	 *
	 * GET /api/  postschangelog
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'search')
			->setFilters($this->_filters());

			Kohana::$log->add(Log::INFO, print_r($this->_usecase, true));

	}

}
