<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Api_Forms extends Ushahidi_Api {

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
		
		$form = ORM::factory('Form')->values($post);
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base form data
			$form->check();

			// Are form groups defined?
			if ( isset($post['groups']) )
			{
				// Yes, loop through and validate each group
				foreach ($post['groups'] as $group)
				{
					$_group = ORM::factory('Form_Group')->values($group);
					$_group->check();

					// Are form attributes defined?
					if ( isset($group['attributes']) )
					{
						// Yes, loop through and validate each form attribute
						foreach ($group['attributes'] as $attribute)
						{
							$_attribute = ORM::factory('Form_Attribute')->values($attribute);
							$_attribute->check();
						}
					}
				}
			}

			// Validates ... so save
			$form->values($post, array(
				'name', 'description', 'type'
				));
			$form->save();

			if ( isset($post['groups']) )
			{
				foreach ($post['groups'] as $group)
				{
					$_group = ORM::factory('Form_Group');
					if ( isset($group['label']) )
					{
						$_group->label = $group['label'];
					}
					if ( isset($group['priority']) )
					{
						$_group->priority = $group['priority'];
					}
					$_group->form_id = $form->id;
					$_group->save();


					if ( isset($group['attributes']) )
					{
						foreach ($group['attributes'] as $attribute)
						{
							$_attribute = ORM::factory('Form_Attribute');
							$_attribute->values($attribute, array(
								'key', 'label', 'input', 'type'
								));
							$_attribute->options = ( isset($attribute['options']) ) ? json_encode($attribute['options']) : NULL;
							$_attribute->form_id = $form->id;
							$_attribute->form_group_id = $_group->id;
							$_attribute->save();
						}
					}
				}
			}

			// Response is the complete form
			$this->_response_payload = $this->form($form);
		}
		catch (ORM_Validation_Exception $e)
		{
			// @todo throw 400
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
		}
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
			$results[] = $this->form($form);
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
		$form_id = $this->request->param('id', 0);

		// Respond with form
		$form = ORM::factory('Form', $form_id);
		$this->_response_payload = $this->form($form);
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
		$form_id = $this->request->param('id', 0);
		$post = $this->_request_payload;
		
		$form = ORM::factory('form', $form_id)->values($post);
		
		// Set form id to ensure sane response if form doesn't exist yet.
		$form->id = $form_id;
		
		
		// Validation - cycle through nested models 
		// and perform in-model validation before
		// saving
		try
		{
			// Validate base form data
			$form->check();


			// Validates ... so save
			$form->values($post, array(
				'name', 'description', 'type'
				));
			$form->save();


			// Response is the complete form
			$this->_response_payload = $this->form($form);
		}
		catch (ORM_Validation_Exception $e)
		{
			// @todo throw 400
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
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
		$form_id = $this->request->param('id', 0);
		$form = ORM::factory('Form', $form_id);
		$this->_response_payload = array();
		if ( $form->loaded() )
		{
			// Return the form we just deleted (provides some confirmation)
			$this->_response_payload = $this->form($form);
			$form->delete();
		}
	}

	/**
	 * Retrieve a single form, along with all its 
	 * groups and attributes
	 * 
	 * @param $form object - form model
	 * @return array $response
	 */
	public function form($form = NULL)
	{
		$response = array();
		if ( $form->loaded() )
		{
			$response = array(
				'url' => url::site('api/v2/forms/'.$form->id, Request::current()),
				'id' => $form->id,
				'name' => $form->name,
				'description' => $form->description,
				'type' => $form->type,
				'groups' => array()
				);

			foreach ($form->form_groups->find_all() as $group)
			{
				$response['groups'][] = Controller_API_Forms_Groups::group($group);
			}
		}
		else
		{
			// @todo throw 404
			$response = array(
				'errors' => array(
					'Form does not exist'
					)
				);
		}

		return $response;
	}
}
