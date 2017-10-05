<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi API Post Lock Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_API_Posts_ChangeLog extends Ushahidi_Rest {
	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::POST  => 'post',
		Http_Request::OPTIONS => 'options'
	);
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
  		Kohana::$log->add(Log::INFO, 'Adding a log entry manually...');

  		//TODO: QUESTION? should we append the current user here?
  		$edited_payload = $this->_payload();
  		$user = service('session.user');
  		Kohana::$log->add(Log::INFO, 'Current user:'.print_r($user->getId(), true));
  		$edited_payload['user_id'] = $user->getId();
  		$this->_usecase = service('factory.usecase')
  			->get($this->_resource(), 'create')
  			->setPayload($edited_payload);
  	}


  	public function action_get_index_collection()
  	{
  		Kohana::$log->add(Log::INFO, 'Identifiers: '.print_r($this->_identifiers(), true));

  		$this->_usecase = service('factory.usecase')
  			->get($this->_resource(), 'search')
  			->setFilters($this->_filters());
  	}


}
