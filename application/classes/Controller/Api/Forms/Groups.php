<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Groups Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Forms_Groups extends Ushahidi_Api {

	/**
	 * Require forms scope - extra scope for groups seems unnecessary
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'forms';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'form_groups';

		// Check form exists
		$form_id = $this->request->param('form_id', 0);
		$form = ORM::factory('Form', $form_id);
		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Form does not exist. ID: \':id\'', array(
				':id' => $form_id,
			));
		}

		$this->_resource = ORM::factory('Form_Group')
			->set('form_id', $form_id);

		// Get group
		if ($id = $this->request->param('id', 0))
		{
			$group = ORM::factory('Form_Group')
				->where('form_id', '=', $form_id)
				->where('id', '=', $id)
				->find();

			if (! $group->loaded())
			{
				throw new HTTP_Exception_404('Form Group does not exist. ID: \':id\'', array(
					':id' => $id,
				));
			}

			$this->_resource = $group;
		}
	}

	/**
	 * Create a new group
	 *
	 * POST /api/forms/:form_id/groups
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;

		$group = $this->resource();

		$this->create_or_update($group, $post);
	}

	/**
	 * Retrieve all groups
	 *
	 * GET /api/forms/:form_id/groups
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$results = array();

		$groups = ORM::factory('Form_Group')
			->order_by('id', 'ASC')
			->where('form_id', '=', $form_id)
			->find_all();

		$count = $groups->count();

		foreach ($groups as $group)
		{
			// Check if user is allowed to access this group
			if ($this->acl->is_allowed($this->user, $group, 'get') )
			{
				$result = $group->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($group);
				$results = $result;
			}
		}

		// Respond with groups
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Retrieve a group
	 *
	 * GET /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$group = $this->resource();

		// Respond with group
		$this->_response_payload =  $group->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update a single group
	 *
	 * PUT /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$post = $this->_request_payload;

		$group = $this->resource();

		$this->create_or_update($group, $post);
	}

	/**
	 * Save Group
	 *
	 * @param Model_Form_Group $group
	 * @param array $post POST data
	 */
	protected function create_or_update($group, $post)
	{
		// Load post values into group model
		$group->values($post, array(
			'label', 'priority', 'icon'
			));

		// Validation - perform in-model validation before saving
		try
		{
			// Validate base group data
			$group->check();

			// Validates ... so save
			$group->save();

			// Response is the complete form
			$this->_response_payload = $group->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($group);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Delete a single group
	 *
	 * DELETE /api/forms/:form_id/groups/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$id = $this->request->param('id');
		$form_id = $this->request->param('form_id');

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		$this->_response_payload = array();
		if ( $group->loaded() )
		{
			// Return the group we just deleted (provides some confirmation)
			$this->_response_payload = $group->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$group->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Group does not exist. Group ID: \':id\'', array(
				':id' => $id,
			));
		}
	}
}
