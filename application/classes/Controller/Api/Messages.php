<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Messages Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Messages extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::POST    => 'post',   // Typically Create..
		Http_Request::GET     => 'get',
		Http_Request::PUT     => 'put',    // Typically Update..
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'messages';
	}

	/**
	 * GET post created from message
	 *
	 * GET /messages/:id/post
	 */
	public function action_get_post()
	{
		// @todo make this a proper use case
		$message = service('repository.message')->get($this->request->param('id'));

		if ($message->post_id === NULL)
		{
			throw HTTP_Exception::factory(404, 'Post does not exist for this message');
		}

		$uri = Route::get('api')->uri(array(
			'controller' => 'Posts',
			'id' => $message->post_id
		));

		// Send a sub request to api/posts/:id
		$response = Request::factory($uri)
			->headers($this->request->headers()) // Forward current request headers to the sub request
			->execute();

		// Override response to ensure status code etc is set
		$this->response = $response;

		// Return a JSON formatted response
		$this->_response_payload  = json_decode($response->body(), TRUE);
	}
}
