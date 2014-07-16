<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi API Media Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Media extends Ushahidi_Api
{

	/**
	 * @var array List of HTTP methods which support body content
	 */
	protected $_methods_with_body_content = array
	(
		Http_Request::PUT,
	);

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'media';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'media';

		$this->_resource = ORM::factory('Media');

		// Get post
		if ($media_id = $this->request->param('id', 0))
		{
			// Respond with set
			$media = ORM::factory('Media', $media_id);

			if (! $media->loaded())
			{
				throw new HTTP_Exception_404('Media does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $media;
		}
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
		$repo   = service('repository.media');
		$parser = service('parser.media.search');
		$format = service('formatter.entity.media');

		$input = $parser($this->request->query());

		// this probably belongs in the parser, or should just return the
		// order/limit params as an array for the search call
		$this->_prepare_order_limit_params();

		$media = $repo->search($input, [
			'orderby' => $this->_record_orderby,
			'order' => $this->_record_order,
			'offset' => $this->_record_offset,
			'limit' => $this->_record_limit,
			]);
		$count = count($media);

		$results = [];
		foreach ($media as $file)
		{
			// Check if user is allowed to access this tag
			// todo: fix the ACL layer so that it can consume an Entity
			if ($this->acl->is_allowed($this->user, $file->getResource(), 'get') )
			{
				$result = $format($file);
				$result['allowed_methods'] = $this->_allowed_methods($file->getResource());
				$results[] = $result;
			}
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results,
			)
			+ $this->_get_paging_parameters();
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
		$format  = service('formatter.entity.media');
		$repo    = service('repository.media');
		$mediaid = $this->request->param('id') ?: 0;
		$media   = $repo->get($mediaid);

		if (!$media->id)
		{
			throw new HTTP_Exception_404('Media :id does not exist', array(
				':id' => $mediaid,
			));
		}

		$this->_response_payload = $format($media);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
		$format  = service('formatter.entity.media');
		$parser  = service('parser.media.create');
		$usecase = service('usecase.media.create');

		// Does not use `request_payload`, as uploads are not sent via the API,
		// but rather as a "normal" web request.
		$request = array_merge($_FILES, $this->request->post());

		if ($this->user)
		{
			// Inject the user id into the request for association.
			$request['user_id'] = $this->user->id;
		}

		try
		{
			$input = $parser($request);
			$media = $usecase->interact($input);
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}

		$this->_response_payload = $format($media);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
		$media = $this->resource();

		$this->_response_payload = array();

		if ($media->loaded())
		{
			// Return the media that is about to be deleted
			$this->_response_payload = $media->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();

			// Delete the details from the db
			$media->delete();
		}
	}
}
