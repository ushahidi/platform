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

		$this->_resource = ORM::factory('Tag');

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
		$post = $this->_request_payload;

		$tag = $this->resource();

		$this->create_or_update_tag($tag, $post);
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
		$parser = service('parser.tag');
		$format = service('formatter.entity.api');
		$usecase = service('usecase.api.tag.collection')
			->orderBy($this->_record_orderby, $this->_record_order)
			->limit($this->_record_limit)
			->offset($this->_record_offset);

		if ($q = $this->request->query('q'))
		{
			$usecase->query('tag', $q);
		}

		try
		{
			$tag = $parser($this->request->query());
			$found = $usecase->search($tag);
		}
		catch (Ushahidi\Exception\Validator $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}

		// This is generic right now, as per-entity ACL checks are not possible
		// (or required) at this point.
		$allowed = ['allowed_methods' => $this->_allowed_methods()];

		$results = array();
		foreach ($found as $tag)
		{
			$results[] = $format($tag) + $allowed;
		}
		$count = count($results);

		$this->_response_payload = compact('count', 'results') + $this->_get_paging_parameters();
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
		$tag = $this->resource();

		$this->_response_payload = $tag->for_api();
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
		$post = $this->_request_payload;

		$tag = $this->resource();

		$this->create_or_update_tag($tag, $post);
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
		$tag = $this->resource();
		$this->_response_payload = array();
		if ( $tag->loaded() )
		{
			// Return the form we just deleted (provides some confirmation)
			$this->_response_payload = $tag->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$tag->delete();
		}
	}

	/**
	 * Save tags
	 *
	 * @param Tag_Model $tag
	 * @param array $post POST data
	 */
	protected function create_or_update_tag($tag, $post)
	{
		// Check
		if (isset($post['parent']))
		{
			// If we have a parent array with url/id
			if (is_array($post['parent']) AND isset($post['parent']['id']))
			{
				$post['parent_id'] = $post['parent']['id'];
			}
			// If parent is numeric, assume its an id
			elseif (Valid::numeric($post['parent']))
			{
				$post['parent_id'] = $post['parent'];
			}
			else
			{
				// Try to find parent by slug
				$parent = ORM::factory('Tag', array('slug' => $post['parent']));
				if ($parent->loaded())
				{
					$post['parent_id'] = $parent->id;
				}
			}
		}

		$tag->values($post, array(
			'tag', 'slug', 'type', 'parent_id', 'priority', 'color', 'description'
			));

		// Validation - cycle through nested models
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base form data
			$tag->check();

			$tag->save();

			// Response is the complete form
			$this->_response_payload = $tag->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}
}
