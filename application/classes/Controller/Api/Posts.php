<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Api\Endpoint;

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
		$endpoint = service('factory.endpoint')->get('posts', 'search');

		$extra_params = [
			'type' => $this->_type,
			'parent' => $this->request->param('parent_id', NULL)
		];

		$this->_restful($endpoint, $extra_params +  $this->request->query());
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
		$endpoint = service('factory.endpoint')->get('posts', 'read');

		$this->_restful($endpoint, $this->request->param());
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
		$endpoint = service('factory.endpoint')->get('posts', 'delete');

		$this->_restful($endpoint, $this->request->param());
	}

	/**
	 * Run an Endpoint request sequence and convert application exceptions into
	 * Kohana HTTP exceptions.
	 * @throws HTTP_Exception_400
	 * @throws HTTP_Exception_403
	 * @throws HTTP_Exception_404
	 * @param  Ushahidi\Endpoint $endpoint
	 * @param  Array $request
	 * @return void
	 */
	protected function _restful(Endpoint $endpoint, Array $request)
	{
		try
		{
			$this->_response_payload = $endpoint->run($request);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
	}
}
