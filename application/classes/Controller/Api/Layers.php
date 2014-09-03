<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Layers Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Layers extends Ushahidi_Api {

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'id';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'active', 'type');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'layers';

	/**
	 * Disable global ACL check - using Authorizer class instead
	 */
	protected $resource_acl_check = FALSE;

	/**
	 * Retrieve All Layers
	 *
	 * GET /api/layers
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$repo   = service('repository.layer');
		$parser = service('parser.layer.search');
		$format = service('formatter.entity.layer');
		$authorizer = service('tool.authorizer.layer');

		$input = $parser($this->request->query());

		$layers = $repo->search($input);


		$results = [];
		foreach ($layers as $layer)
		{
			// Check if user is allowed to access this layer
			if($authorizer->isAllowed($layer, 'get', $this->user))
			{
				$result = $format($layer);
				$result['allowed_methods'] = $this->_allowed_methods($layer->getResource());
				$results[] = $result;
			}
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => count($layers),
			'results' => $results,
			)
			+ $this->_get_paging_for_input($input);
	}

	/**
	 * Retrieve A Layer
	 *
	 * GET /api/layers/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$repo   = service('repository.layer');
		$format = service('formatter.entity.api');
		$authorize = service('tool.authorizer.layer');
		$layerid  = $this->request->param('id') ?: 0;
		$layer    = $repo->get($layerid);

		if (!$layer->id)
		{
			throw new HTTP_Exception_404('Layer :id does not exist', array(
				':id' => $layerid,
			));
		}

		if (!$authorize->isAllowed($layer, 'get', $this->user))
			throw new AuthorizerException(sprintf('User %s is not allowed to access the layer  %s',
					$layer
					));

			$this->_response_payload = $format($layer);
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}
}
