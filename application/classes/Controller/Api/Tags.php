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
		$format  = service('formatter.entity.tag');
		$parser  = service('parser.tag.create');
		$usecase = service('usecase.tag.create');

		try
		{
			$request = $parser($this->_request_payload);
			$tag = $usecase->interact($request);
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

		$input = $parser($this->request->query());
		
		// this probably belongs in the parser, or should just return the
		// order/limit params as an array for the search call
		$this->_prepare_order_limit_params();

		$tags = $repo->search($input, [
			'orderby' => $this->_record_orderby,
			'order' => $this->_record_order,
			'offset' => $this->_record_offset,
			'limit' => $this->_record_limit,
			]);

		$count = count($tags);

		$results = [];
		foreach ($tags as $tag)
		{
			// Check if user is allowed to access this tag
			// todo: fix the ACL layer so that it can consume an Entity
			if ($this->acl->is_allowed($this->user, $tag->getResource(), 'get') )
			{
				$result = $format($tag);
				$result['allowed_methods'] = $this->_allowed_methods($tag->getResource());
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
		$tagid  = $this->request->param('id') ?: 0;
		$tag    = $repo->get($tagid);

		if (!$tag->id)
		{
			throw new HTTP_Exception_404('Tag :id does not exist', array(
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
		$format  = service('formatter.entity.api');
		$parser  = service('parser.tag.update');
		$usecase = service('usecase.tag.update');

		$tagid = $this->request->param('id');
		$tag = service('repository.tag')->get($tagid);

		if (!$tag->id)
		{
			throw new HTTP_Exception_404('Tag :id does not exist', array(
				':id' => $tagid,
			));
		}

		try
		{
			$request = $parser($this->_request_payload);
			$usecase->interact($tag, $request);
		}
		catch (Ushahidi\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}

		$this->_response_payload = $format($tag);
		$this->_response_payload['updated_fields'] = $usecase->getUpdated();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
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
