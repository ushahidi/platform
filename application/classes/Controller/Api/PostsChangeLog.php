<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_PostsChangeLog extends Ushahidi_Rest {


	protected function _scope()
	{
		return 'posts';
	}

	protected function _resource()
	{
		return 'posts_changelog';
	}

	public function action_post_index_collection()
	{
		Kohana::$log->add(Log::INFO, 'Adding a log entry manually...with params:'.print_r($this->request->param(), true));
		//parent::action_post_index_collection();
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'create')
			->setPayload($this->_payload());

			//$this->_usecase->setIdentifiers($this->request->param());
	}


}
