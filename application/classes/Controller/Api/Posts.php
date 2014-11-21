<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts extends Ushahidi_Api {

	/**
	 * @var int Post Parent ID
	 */
	protected $_parent_id = NULL;

	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'updated', 'title');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'posts';

	protected $_boundingbox = FALSE;

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		// Get dummy post for access check
		$this->_resource = ORM::factory('Post')
			->set('status', 'published');

		// Get parent if we have one
		if ($this->_parent_id = $this->request->param('parent_id', NULL))
		{
			// Check parent post exists
			$parent = ORM::factory('Post', $this->_parent_id);
			if (! $parent->loaded())
			{
				throw new HTTP_Exception_404('Parent Post does not exist. Post ID: \':id\'', array(
					':id' => $this->_parent_id,
				));
			}

			// Use parent post for access check if no individual post set
			// This happens when getting all translations/revisions/updates..
			$this->_resource = $parent;
		}

		// Get post
		if ($post_id = $this->request->param('id', 0))
		{
			$post = ORM::factory('Post')
				->where('id', '=', $post_id)
				->where('type', '=', $this->_type);
			if ($this->_parent_id)
			{
				$post->where('parent_id', '=', $this->_parent_id);
			}
			$post = $post->find();

			if (! $post->loaded())
			{
				throw new HTTP_Exception_404('Post does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $post;
		}
	}

	/**
	 * Create A Post
	 *
	 * POST /api/posts
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$format = service('factory.formatter')->get('posts', 'create');
		$write_parser = service('factory.parser')->get('posts', 'create');
		$usecase = service('factory.usecase')->get('posts', 'create');

		$usecase->setType($this->_type);
		$usecase->setParent($this->request->param('parent_id', NULL));

		$request = $this->_request_payload;

		try
		{
			$write_data = $write_parser($this->_request_payload);
			$post = $usecase->interact($write_data);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}

		$this->_response_payload = $format($post);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Retrieve All Posts
	 *
	 * GET /api/posts
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$repo   = service('factory.repository')->get('posts');
		$parser = service('factory.parser')->get('posts', 'search');
		$format = service('factory.formatter')->get('posts', 'read');
		$authorizer = service('factory.authorizer')->get('posts');

		// this probably belongs in the parser, or should just return the
		// order/limit params as an array for the search call
		$this->_prepare_order_limit_params();

		$sorting = [
			'orderby' => $this->_record_orderby,
			'order' => $this->_record_order,
			'offset' => $this->_record_offset,
			'limit' => $this->_record_limit,
			'type' => $this->_type,
			'parent' => $this->_parent_id
		];
		$input = $parser($sorting + $this->request->query());

		$repo->setSearchParams($input);

		$posts = $repo->getSearchResults();
		$total = $repo->getSearchTotal();

		$results = [];
		foreach ($posts as $post)
		{
			// Check if user is allowed to access this post
			// @todo preload user entity, avoid multiple queries
			if ( $authorizer->isAllowed($post, 'read') )
			{
				$result = $format($post);
				// @todo check with authorizer instead
				$result['allowed_methods'] = $this->_allowed_methods($post->getResource());
				$results[] = $result;
			}
		}

		// Count actual results since they're filtered by access check
		$count = count($results);

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'total_count' => $total,
			'results' => $results,
			)
			+ $this->_get_paging_parameters();
	}

	/**
	 * Retrieve A Post
	 *
	 * GET /api/posts/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$format = service('factory.formatter')->get('posts', 'read');
		$read_parser = service('factory.parser')->get('posts', 'read');
		$usecase = service('factory.usecase')->get('posts', 'read');

		$request = $this->_request_payload;

		$read = $this->request->param();

		try
		{
			$read_data = $read_parser($read);
			$post = $usecase->interact($read_data);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}

		$this->_response_payload = $format($post);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update A Post
	 *
	 * PUT /api/posts/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$format = service('factory.formatter')->get('posts', 'update');
		$read_parser = service('factory.parser')->get('posts', 'read');
		$write_parser = service('factory.parser')->get('posts', 'update');
		$usecase = service('factory.usecase')->get('posts', 'update');

		$request = $this->_request_payload;

		$read = $this->request->param();

		try
		{
			$write_data = $write_parser($this->_request_payload);
			$read_data = $read_parser($read);
			$post = $usecase->interact($read_data, $write_data);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}

		$this->_response_payload = $format($post);
		$this->_response_payload['updated_fields'] = $usecase->getUpdated();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Delete A Post
	 *
	 * DELETE /api/posts/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$format = service('factory.formatter')->get('posts', 'delete');
		$read_parser = service('factory.parser')->get('posts', 'delete');
		$usecase = service('factory.usecase')->get('posts', 'delete');

		$request = $this->_request_payload;

		$read = $this->request->param();

		try
		{
			$read_data = $read_parser($read);
			$post = $usecase->interact($read_data);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}

		$this->_response_payload = $format($post);
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}
}
