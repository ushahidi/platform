<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Export Execute CLI Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Ushahidi\Core\Tool\Verifier;

class Controller_Api_External extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'external';
	}

	public function before()
	{
		parent::before();
		$data = $this->_request_payload;
		$api_key = isset($data['api_key']) ? $data['api_key'] : null;

		if ($this->request->method() === 'GET') {
			$data = [];
			$api_key = $this->request->query('api_key');
		} 

		$signature = $this->request->headers('X-Ushahidi-Signature');
		
		$shared_secret = getenv('PLATFORM_SHARED_SECRET');
		$fullURL = URL::site(Request::detect_uri(), TRUE) . URL::query();

		$verifier = new Verifier($signature, $api_key, $shared_secret, $fullURL, $data);

		if (!$verifier->verified()) {
			throw HTTP_Exception::factory(403, 'Forbidden');
		}
	}

	protected function _is_auth_required()
	{
		return false;
	}


	public function setExternalAuth() {
		$this->_usecase->setAuthorizer(service('authorizer.external_auth'));
	}
	/**
	 * Create An Entity
	 *
	 * POST /api/foo
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		parent::action_post_index_collection();
		$this->setExternalAuth();
	}

	/**
	 * Retrieve All Entities
	 *
	 * GET /api/foo
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		parent::action_get_index_collection();
		$this->setExternalAuth();
	}

	/**
	 * Retrieve An Entity
	 *
	 * GET /api/foo/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		parent::action_get_index();
		$this->setExternalAuth();
	}

	/**
	 * Update An Entity
	 *
	 * PUT /api/foo/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		parent::action_put_index();
		$this->setExternalAuth();
	}

	/**
	 * Delete An Entity
	 *
	 * DELETE /api/foo/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		parent::action_delete_index();
		$this->setExternalAuth();
	}
}
