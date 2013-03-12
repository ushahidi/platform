<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Abstract Ushahidi API Forms Groups Controller Class
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

abstract class Ushahidi_Controller_API_Forms_Groups extends Ushahidi_API {

	/**
	 * Create a new group
	 * 
	 * POST /api/forms/:form_id/groups
	 * 
	 * @return void
	 */
	public function action_post_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$results = array();
		$post = $this->_request_payload;
		
		$form = ORM::factory('Form', $form_id);
		
		if ( ! $form->loaded())
		{
			// @todo throw 400 or 404
			$this->_response_payload = array(
				'errors' => array(
					'Form does not exist'
					)
				);
			return;
		}
		
		$group = ORM::factory('Form_Group')->values($post);
		$group->form_id = $form_id;
		
		// Validation - perform in-model validation before saving
		try
		{
			// Validate base group data
			$group->check();

			// Validates ... so save
			$group->values($post, array(
				'label', 'priority'
				));
			$group->save();

			// Response is the complete form
			$this->_response_payload = $group->for_api();
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
			$results[] = $group->for_api();
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
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('id');
		$results = array();

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		// Respond with group
		$this->_response_payload =  $group->for_api();
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
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('id');
		$results = array();
		$post = $this->_request_payload;

		$group = ORM::factory('Form_Group')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();
		
		if ( ! $group->loaded())
		{
			// @todo throw 404
			$this->_response_payload = array(
				'errors' => array(
					'Group does not exist'
					)
				);
			return;
		}
		
		// Load post values into group model
		$group->values($post);
		
		$group->id = $id;
		
		// Validation - perform in-model validation before saving
		try
		{
			// Validate base group data
			$group->check();

			// Validates ... so save
			$group->values($post, array(
				'label', 'priority'
				));
			$group->save();

			// Response is the complete form
			$this->_response_payload = $group->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
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
			$group->delete();
		}
	}
	
	/**
	 * Retrieve group's attributes
	 * 
	 * GET /api/forms/:form_id/groups/:id/attributes
	 * 
	 * @return void
	 */
	public function action_get_attributes()
	{
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('id');
		$results = array();

		$attributes = ORM::factory('Form_Attribute')
			->order_by('id', 'ASC')
			->where('form_id', '=', $form_id)
			->where('form_group_id', '=', $id)
			->find_all();
		
		$count = $attributes->count();

		foreach ($attributes as $attribute)
		{
			$results[] = $attribute->for_api();
		}

		// Respond with attributes
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}
	
	/**
	 * Add new attribute to group
	 * 
	 * POST /api/forms/:form_id/groups/:id/attributes
	 * 
	 * @todo share code between this and POST /api/forms/:form_id/attributes
	 * @return void
	 */
	public function action_post_attributes()
	{
		$form_id = $this->request->param('form_id');
		$group_id = $this->request->param('id');
		$results = array();
		$post = $this->_request_payload;
		
		$form = ORM::factory('Form', $form_id);
		
		if ( ! $form->loaded())
		{
			// @todo throw 400 or 404
			$this->_response_payload = array(
				'errors' => array(
					'Form does not exist'
					)
				);
			return;
		}
		
		$group = ORM::factory('Form_Group', $group_id);
		
		if ( ! $group->loaded())
		{
			// @todo throw 400 or 404
			$this->_response_payload = array(
				'errors' => array(
					'Group does not exist'
					)
				);
			return;
		}
		
		$attribute = ORM::factory('Form_Attribute')->values($post);
		$attribute->form_id = $form_id;
		$attribute->form_group_id = $group_id;
		
		// Validation - perform in-model validation before saving
		try
		{
			// Validate base group data
			$attribute->check();

			// Validates ... so save
			$attribute->values($post, array(
				'key', 'label', 'input', 'type'
				));
			$attribute->save();

			// Response is the complete form
			$this->_response_payload = $attribute->for_api();
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
}