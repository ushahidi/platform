<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ushahidi API Forms Attributes Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

/**
 * Attributes API Controller
 */
class Controller_Api_Attributes extends Ushahidi_Api {

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
	 * Create a new attribute
	 *
	 * POST /api/attributes
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$results = array();
		$post = $this->_request_payload;

		// Check form/form_group - only allow creating attributes with a form_group
		// unpack form_group to get form_group_id
		if (isset($post['form_group']))
		{
			if (is_array($post['form_group']) AND isset($post['form_group']['id']))
			{
				$post['form_group_id'] = $post['form_group']['id'];
			}
			elseif (! is_array($post['form_group']))
			{
				$post['form_group_id'] = $post['form_group'];
			}
		}

		if (empty($post["form_group_id"]))
		{
			throw new HTTP_Exception_400('No form_group specified');
		}

		$group = ORM::factory('Form_Group', $post["form_group_id"]);

		if ( ! $group->loaded())
		{
			throw new HTTP_Exception_400('Invalid Form Group ID. \':id\'', array(
				':id' => $post["form_group_id"],
			));
		}

		$attribute = ORM::factory('Form_Attribute');

		$this->create_or_update($attribute, $post, $group);
	}

	/**
	 * Retrieve all attributes
	 *
	 * GET /api/attributes
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$attributes = ORM::factory('Form_Attribute')
			->order_by('id', 'ASC')
			->find_all();

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
	 * Retrieve an attribute
	 *
	 * GET /api/attributes/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$attribute = $this->resource();

		$this->_response_payload = $attribute->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update a single attribute
	 *
	 * PUT /api/attributes/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$post = $this->_request_payload;

		$attribute = $this->resource();

		$this->create_or_update($attribute, $post);
	}

	/**
	 * Save Attribute
	 *
	 * @param Model_Form_Attribute $attribute
	 * @param array $post POST data,
	 * @param Model_Form_Group $group
	 */
	protected function create_or_update($attribute, $post, $group = FALSE)
	{
		// Load post values into group model
		$attribute->values($post, array(
			'key', 'label', 'input', 'type', 'options', 'required', 'default', 'unique', 'priority', 'cardinality'
			));

		// Validation - perform in-model validation before saving
		try
		{
			// Validate base attribute data
			$attribute->check();

			// Validates ... so save
			$attribute->save();

			// Add to group
			if ($group)
			{
				$group->add('form_attributes', $attribute);
			}

			// Response is the complete form
			$this->_response_payload = $attribute->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($attribute);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Delete a single attribute
	 *
	 * DELETE /api/attributes/:id
	 *
	 * @return void
	 */
	public function action_delete_index()
	{
		$id = $this->request->param('id');

		$attribute = ORM::factory('Form_Attribute')
			->where('id', '=', $id)
			->find();

		$this->_response_payload = array();
		if ( $attribute->loaded() )
		{
			// Return the attribute we just deleted (provides some confirmation)
			$this->_response_payload = $attribute->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($attribute);
			$attribute->delete();
		}
		else
		{
			throw new HTTP_Exception_404('Attribute does not exist. Attribute ID: \':id\'', array(
				':id' => $id,
			));
		}
	}
}