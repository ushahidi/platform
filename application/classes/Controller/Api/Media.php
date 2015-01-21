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
	 * Create a media
	 *
	 * POST /api/media
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_scope(), 'create')
			// Does not use `request_payload`, as uploads are not sent via the API,
			// but rather as a "normal" web request.
			->setPayload(array_merge($_FILES, $this->request->post()));
	}
}
