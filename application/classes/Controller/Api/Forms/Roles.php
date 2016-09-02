<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Roles Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Forms_Roles extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::PUT     => 'put',    // Typically Update..
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'forms';
	}

	protected function _resource()
	{
		return 'form_roles';
	}

	// Ushahidi_Rest
	public function action_get_index_collection()
	{
		parent::action_get_index_collection();

		$this->_usecase->setIdentifiers($this->request->param());
		$this->_usecase->setFilters($this->request->query() + [
			'form_id' => $this->request->param('form_id')
			]);
	}

	// Ushahidi_Rest
	public function action_put_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'update_collection')
			->setIdentifiers($this->_identifiers())
			->setPayload($this->_payload());
	}

}
