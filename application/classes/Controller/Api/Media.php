<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi API Media Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Media extends Ushahidi_Rest
{

	/**
	 * @var array List of HTTP methods which support body content
	 */
	protected $_methods_with_body_content = array
	(
		Http_Request::PUT,
	);

	protected function _scope()
	{
		return 'media';
	}

	/**
	 * Retrieve all media
	 *
	 * GET /api/media
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$endpoint = service('endpoint.media.get.collection');
		$request = $this->request->query();

		$this->_restful($endpoint, $request);
	}

	/**
	 * Retrieve a Media
	 *
	 * GET /api/media/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$endpoint = service('endpoint.media.get.index');
		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}

	/**
	 * Create a media
	 *
	 * POST /api/media
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('endpoint.media.post.collection');

		// Does not use `request_payload`, as uploads are not sent via the API,
		// but rather as a "normal" web request.
		$request = array_merge($_FILES, $this->request->post());

		$this->_restful($endpoint, $request);
	}

	/**
	 * Delete a media
	 *
	 * DELETE /api/media/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$endpoint = service('endpoint.media.delete.index');

		$request = ['id' => $this->request->param('id')];

		$this->_restful($endpoint, $request);
	}
}
