<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Form Group Attributes Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_Forms_Groups_Attributes extends Ushahidi_Api {

	/**
	 * Require forms scope - extra scope for attribute seems unnecessary
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

		$this->_resource = 'form_attributes';

		// Check form exists
		$form_id = $this->request->param('form_id', 0);
		$form = ORM::factory('Form', $form_id);
		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Form does not exist. ID: \':id\'', array(
				':id' => $form_id,
			));
		}

		// Check group exists
		$group_id = $this->request->param('group_id', 0);
		$group = ORM::factory('Form_Group', $group_id);
		if ( ! $group->loaded())
		{
			throw new HTTP_Exception_404('Form Group does not exist. ID: \':id\'', array(
				':id' => $group_id,
			));
		}

		$this->_resource = ORM::factory('Form_Attribute');

		// Get attribute
		if ($id = $this->request->param('id', 0))
		{
			$attribute = ORM::factory('Form_Attribute', $id);

			if (! $attribute->loaded())
			{
				throw new HTTP_Exception_404('Form Attribute does not exist. ID: \':id\'', array(
					':id' => $id,
				));
			}

			$this->_resource = $attribute;
		}
	}


	/**
	 * Add new attribute to group
	 *
	 * POST /api/forms/:form_id/groups/:id/attributes
	 *
	 * @todo share code between this and POST /api/attributes
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$group_id = $this->request->param('group_id');
		$results = array();
		$post = $this->_request_payload;

		$form = ORM::factory('Form', $form_id);

		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Invalid Form ID. \':id\'', array(
				':id' => $form_id,
			));
		}

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $group_id)
			->find();

		if (! $group->loaded())
		{
			throw new HTTP_Exception_404('Group does not exist. Group ID: \':id\'', array(
				':id' => $group_id,
			));
		}

		// If we're trying to add an existing attribute
		if (! empty($post['id']))
		{
			$attribute = ORM::factory('Form_Attribute', $post['id']);

			if (! $attribute->loaded())
			{
				throw new HTTP_Exception_400('Attribute does not exist. Attribute ID: \':id\'', array(
				':id' => $post['id'],
			));
			}

			// Add to group (if not already)
			if (!$group->has('form_attributes', $attribute))
			{
				$group->add('form_attributes', $attribute);
			}

			// Response is the complete form
			$this->_response_payload = $attribute->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($attribute);

			return;
		}
		// Else: create a new attribute and add it to the group
		else
		{

			// Validation - perform in-model validation before saving
			try
			{
				$post['form_group_id'] = $group_id;
				$response = Request::factory('api/v'.Ushahidi_Api::version().'/attributes/')
					->method(Request::POST)
					->body(json_encode($post))
					->headers($this->request->headers()) // Forward headers for OAuth checks
					->execute();
				// Override response to ensure status code etc is set
				$this->response = $response;

				// Response is the complete form
				$this->_response_payload = json_decode($response->body());
			}
			catch (ORM_Validation_Exception $e)
			{
				throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
					':errors' => implode(', ', Arr::flatten($e->errors('models'))),
				));
			}
		}
	}

	/**
	 * Retrieve group's attributes
	 *
	 * GET /api/forms/:form_id/groups/:id/attributes
	 *
	 * @todo share code between this and GET /api/attributes/:id
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('group_id');
		$results = array();

		$form = ORM::factory('Form', $form_id);

		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Invalid Form ID. \':id\'', array(
				':id' => $form_id,
			));
		}

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		if (! $group->loaded())
		{
			throw new HTTP_Exception_404('Group does not exist. Group ID: \':id\'', array(
				':id' => $id,
			));
		}

		$attributes = $group->form_attributes->find_all();

		$count = $attributes->count();

		foreach ($attributes as $attribute)
		{
			// Check if user is allowed to access this attribute
			if ($this->acl->is_allowed($this->user, $attribute, 'get') )
			{
				$result = $attribute->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($attribute);
				$results[] = $result;
			}
		}

		// Respond with attributes
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Remove a group attribute from the group
	 *
	 * GET /api/forms/:form_id/groups/:id/attributes/:id
	 *
	 * @todo share code between this and POST /api/attributes/:id
	 * @return void
	 */
	public function action_get_index()
	{
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('group_id');
		$attr_id = $this->request->param('id');
		$results = array();

		$form = ORM::factory('Form', $form_id);

		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Invalid Form ID. \':id\'', array(
				':id' => $form_id,
			));
		}

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		if (! $group->loaded())
		{
			throw new HTTP_Exception_404('Group does not exist. Group ID: \':id\'', array(
				':id' => $id,
			));
		}

		$attr = $group->form_attributes->where('form_attribute_id', '=', $attr_id)->find();

		if (! $attr->loaded())
		{
			throw new HTTP_Exception_404('Attribute does not exist or is not a member of this group. Attribute ID: \':id\'', array(
				':id' => $attr_id,
			));
		}

		// Response is the complete attribute
		$this->_response_payload = $attr->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods($attr);
	}

	/**
	 * Remove a group attribute from the group
	 *
	 * DELETE /api/forms/:form_id/groups/:id/attributes/:id
	 *
	 * @todo share code between this and POST /api/attributes/:id
	 * @return void
	 */
	public function action_delete_index()
	{
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('group_id');
		$attr_id = $this->request->param('id');
		$results = array();

		$form = ORM::factory('Form', $form_id);

		if ( ! $form->loaded())
		{
			throw new HTTP_Exception_404('Invalid Form ID. \':id\'', array(
				':id' => $form_id,
			));
		}

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		if (! $group->loaded())
		{
			throw new HTTP_Exception_404('Group does not exist. Group ID: \':id\'', array(
				':id' => $id,
			));
		}

		$attr = $group->form_attributes->where('form_attribute_id', '=', $attr_id)->find();

		if (! $attr->loaded())
		{
			throw new HTTP_Exception_404('Attribute does not exist or is not a member of this group. Attribute ID: \':id\'', array(
				':id' => $attr_id,
			));
		}

		$group->remove('form_attributes', $attr);

		// Response is the complete attribute
		$this->_response_payload = $attr->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods($attr);
	}
}