<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Tags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Tags extends Ushahidi_Api {

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'priority';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'tag', 'slug', 'priority');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'tags';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'tags';

		// Get post
		if ($tag_id = $this->request->param('id', 0))
		{
			// Respond with set
			$tag = ORM::factory('Tag', $tag_id);

			if (! $tag->loaded())
			{
				throw new HTTP_Exception_404('Tag does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $tag;
		}
	}

	/**
	 * Create A Tag
	 *
	 * POST /api/tags
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$endpoint = service('endpoint.tags.post.collection');
		try
		{
			$this->_response_payload = $endpoint->run($this->_request_payload);
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
	}

	/**
	 * Retrieve All Tags
	 *
	 * GET /api/tags
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$repo   = service('repository.tag');
		$parser = service('parser.tag.search');
		$format = service('formatter.entity.tag');
		$authorize = service('tool.authorizer.tag');
		$input = $parser($this->request->query());

		$tags = $repo->search($input);

		$results = [];
		foreach ($tags as $tag)
		{
			// Check if user is allowed to access this tag
			// todo: fix the ACL layer so that it can consume an Entity
			if($authorize->isAllowed($tag, 'get', $this->user))
			{
				$result = $format($tag);
				$result['allowed_methods'] = $this->_allowed_methods($tag->getResource());
				$results[] = $result;
			}
		}

		// Respond with posts
		$this->_response_payload = array(
			'count' => count($results),
			'results' => $results,
			)
			+ $this->_get_paging_for_input($input);
	}

	/**
	 * Retrieve A Tag
	 *
	 * GET /api/tags/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$repo   = service('repository.tag');
		$format = service('formatter.entity.api');
		$authorize = service('tool.authorizer.tag');
		$tagid  = $this->request->param('id') ?: 0;
		$tag    = $repo->get($tagid);

		if (!$tag->id)
		{
			throw new HTTP_Exception_404('Tag :id does not exist', array(
				':id' => $tagid,
			));
		}

		if (! $authorize->isAllowed($tag, 'get', $this->user))
		{
			throw new HTTP_Exception_403('You do not have permission to access tag :id', array(
				':id' => $tagid,
			));
		}

		$this->_response_payload = $format($tag);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update A Tag
	 *
	 * PUT /api/tags/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$endpoint = service('endpoint.tags.put.index');

		$request = $this->_request_payload;
		$request['id'] = $this->request->param('id');

		try
		{
			$this->_response_payload = $endpoint->run($request);
			$this->_response_payload['updated_fields'] = $endpoint->getUpdated();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
		}
		catch (Ushahidi\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
	}

	/**
	 * Delete A Tag
	 *
	 * DELETE /api/tags/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$format  = service('formatter.entity.tag');
		$parser  = service('parser.tag.delete');
		$usecase = service('usecase.tag.delete');

		if (!$this->user OR !$this->user->id)
		{
			throw new HTTP_Exception_401('Cannot delete tag anonymously, please login');
		}

		$request = ['id' => $this->request->param('id')];

		try
		{
			$input = $parser($request);
			$tag   = $usecase->interact($input);
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}

		$this->_response_payload = $format($tag);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}
}
