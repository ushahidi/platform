<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API DataProvider Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_DataProviders extends Ushahidi_Api {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get'
	);

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'dataproviders';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'dataproviders';
	}

	/**
	 * Retrieve All Enabled Data Providers
	 *
	 * GET /api/dataproviders
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		// Get provider type from  the request query
		$type = $this->request->query('type');

		$results = array();

		// Retrieve all enabled providers
		$dataproviders = DataProvider::get_providers();

		foreach ($dataproviders as $dataprovider)
		{
			if ($type AND empty($dataprovider['services'][$type])) continue;

			$results[] = $this->_for_api($dataprovider);

		}

		$count = count($results);

		// Respond with data providers
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
		);
	}

	/**
	 * Retrieve A Provider
	 *
	 * GET /api/providers/:provider
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$provider = $this->request->param('id');

		$data_provider = DataProvider::get_providers($provider);

		if (empty($data_provider))
		{
			throw HTTP_Exception::factory(404, 'Data provider :provider could not be found', array(':provider' => $provider));
		}

		$this->_response_payload = $this->_for_api($data_provider);
	}

	protected function _for_api( array $provider_data)
	{
		foreach ($provider_data['options'] as $name => $input)
		{
			if (isset($input['description']) AND $input['description'] instanceof Closure)
			{
				$provider_data['options'][$name]['description'] = $provider_data['options'][$name]['description']();
			}

			if (isset($input['label']) AND $input['label'] instanceof Closure)
			{
				$provider_data['options'][$name]['label'] = $provider_data['options'][$name]['label']();
			}
		}
		// Append allowed methods to retrieved data providers
		return $provider_data + array(
			'allowed_methods' => $this->_allowed_methods()
		);
	}
}
