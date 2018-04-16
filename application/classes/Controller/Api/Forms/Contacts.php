<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Forms_Contacts extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'forms';
	}

	protected function _resource()
	{
		return 'form_contacts';
	}

	// Get Lock
	public function action_post_index_collection()
	{

		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'create');
		$this->_usecase->setIdentifiers($this->request->param());
		$this->_usecase->setPayload($this->_request_payload);
	}

	// Get Lock
	public function action_get_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'search');
		$this->_usecase->setIdentifiers($this->request->param());
		$this->_usecase
			->setFormatter(service("formatter.entity.form.contactcollection"));
	}
}
