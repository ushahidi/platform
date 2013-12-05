<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Forms extends Ushahidi_Api {

	/**
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

		$this->_resource = 'forms';

		$this->_resource = ORM::factory('Form');

		// Get post
		if ($form_id = $this->request->param('id', 0))
		{
			// Respond with set
			$form = ORM::factory('Form', $form_id);

			if (! $form->loaded())
			{
				throw new HTTP_Exception_404('Form does not exist. ID: \':id\'', array(
					':id' => $this->request->param('id', 0),
				));
			}

			$this->_resource = $form;
		}
	}

	/**
	 * Create A Form
	 *
	 * POST /api/forms
	 *
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$post = $this->_request_payload;

		$form = $this->resource();

		$this->create_or_update($form, $post);
	}

	/**
	 * Retrieve All Forms
	 *
	 * GET /api/forms
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$results = array();

		$forms = ORM::factory('Form')
			->order_by('created', 'ASC')
			->find_all();

		$count = $forms->count();

		foreach ($forms as $form)
		{
			// Check if user is allowed to access this form
			if ($this->acl->is_allowed($this->user, $form, 'get') )
			{
				$result = $form->for_api();
				$result['allowed_methods'] = $this->_allowed_methods($form);
				$results[] = $result;
			}
		}

		// Respond with forms
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Retrieve A Form
	 *
	 * GET /api/forms/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$form = $this->resource();

		$this->_response_payload = $form->for_api();
		$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
	}

	/**
	 * Update A Form
	 *
	 * PUT /api/forms/:id
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$post = $this->_request_payload;

		$form = $this->resource();

		$this->create_or_update($form, $post);
	}

	/**
	 * Save form
	 *
	 * @param Form_Model $form
	 * @param array $post POST data
	 */
	protected function create_or_update($form, $post)
	{
		$form->values($post, array(
			'name', 'description', 'type'
			));

		// Unset groups if we're updating a post
		if ( $form->loaded() AND isset($post['groups']) )
		{
			unset($post['groups']);
		}

		// Validation - cycle through nested models
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base form data
			$form->check();

			// Are form groups defined?
			$_groups = array();
			if ( isset($post['groups']) )
			{
				// Yes, loop through and validate each group
				foreach ($post['groups'] as $group)
				{
					$_group = ORM::factory('Form_Group')->values($group,array(
						'label', 'priority', 'icon'
					));
					$_group->check();

					// Are form attributes defined?
					$_attributes = array();
					if ( isset($group['attributes']) )
					{
						// Yes, loop through and validate each form attribute
						foreach ($group['attributes'] as $attribute)
						{
							// If we're trying to add an existing attribute
							if (! empty($attribute['id']))
							{
								$_attribute = ORM::factory('Form_Attribute', $attribute['id']);

								if (! $_attribute->loaded())
								{
									throw new HTTP_Exception_400('Attribute does not exist. Attribute ID: \':id\'', array(
										':id' => $attribute['id'],
									));
								}
							}
							// Else: create a new attribute and add it to the group
							else
							{
								$_attribute = ORM::factory('Form_Attribute')->values($attribute, array(
									'key', 'label', 'input', 'type', 'options', 'required', 'default', 'unique', 'priority'
									));
								$_attribute->check();
							}
							$_attributes[] = $_attribute;
						}
					}

					$_groups[] = array(
						'group' => $_group,
						'attributes' => $_attributes
					);
				}
			}

			// Validates ... so save
			$form->save();

			// Save groups
			foreach ($_groups as $group)
			{
				$_group = $group['group'];
				$_group
					->set('form_id', $form->id)
					->save();

				// Save attributes
				foreach($group['attributes'] as $_attribute)
				{
					$_attribute->save();

					// Add relation
					$_group->add('form_attributes', $_attribute);
				}
			}

			// Response is the complete form
			$this->_response_payload = $form->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods($form);
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->errors('models'))),
			));
		}
	}

	/**
	 * Delete A Form
	 *
	 * DELETE /api/forms/:id
	 *
	 * @return void
	 * @todo Authentication
	 */
	public function action_delete_index()
	{
		$form = $this->resource();
		$this->_response_payload = array();
		if ( $form->loaded() )
		{
			// Return the form we just deleted (provides some confirmation)
			$this->_response_payload = $form->for_api();
			$this->_response_payload['allowed_methods'] = $this->_allowed_methods();
			$form->delete();
		}
	}
}
