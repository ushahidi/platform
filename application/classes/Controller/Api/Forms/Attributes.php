<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Attributes Controller
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

class Controller_Api_Forms_Attributes extends Ushahidi_Api {

	/**
	 * Create a new attribute
	 * 
	 * POST /api/forms/:form_id/attributes
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
		
		// unpack form_group to get form_group_id
		if (isset($post['form_group']))
		{
			if (is_array($post['form_group']) AND isset($post['form_group']['id']))
			{
				$post['form_group_id'] = $post['form_group']['id'];
			}
			elseif (is_numeric($post['form_group']))
			{
				$post['form_group_id'] = $post['form_group'];
			}
		}
		
		if (empty($post["form_group_id"]))
		{
			// @todo throw 400
			$this->_response_payload = array(
				'errors' => array(
					'No form_group_id specified'
					)
				);
			return;
		}
		
		$group = ORM::factory('Form_Group', $post["form_group_id"]);
		
		if ( ! $group->loaded())
		{
			// @todo throw 400 or 404?
			$this->_response_payload = array(
				'errors' => array(
					'Group does not exist'
					)
				);
			return;
		}
		
		$attribute = ORM::factory('Form_Attribute')->values($post);
		$attribute->form_id = $form_id;
		
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
			// @todo throw 400 or similar
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
		}
	}

	/**
	 * Retrieve all attributes
	 * 
	 * GET /api/forms/:form_id/attributes
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$results = array();

		$attributes = ORM::factory('Form_Attribute')
			->order_by('id', 'ASC')
			->where('form_id', '=', $form_id)
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
	 * Retrieve an attribute
	 * 
	 * GET /api/forms/:form_id/attributes/:id
	 * 
	 * @return void
	 */
	public function action_get_index()
	{
		$id = $this->request->param('id');
		$form_id = $this->request->param('form_id');
		$results = array();

		$attribute = ORM::factory('Form_Attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		if (! $attribute->loaded())
		{
			throw new Http_Exception_404('Attribute does not exist. Attribute ID: \':id\'', array(
				':id' => $id,
			));
		}

		$this->_response_payload = $attribute->for_api();
	}

	/**
	 * Update a single attribute
	 * 
	 * PUT /api/forms/:form_id/attributes/:id
	 * 
	 * @return void
	 */
	public function action_put_index()
	{
		$form_id = $this->request->param('form_id');
		$id = $this->request->param('id');
		$results = array();
		$post = $this->_request_payload;

		$attribute = ORM::factory('Form_Attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		if (! $attribute->loaded())
		{
			throw new Http_Exception_404('Attribute does not exist. Attribute ID: \':id\'', array(
				':id' => $id,
			));
		}
		
		// unpack form_group to get form_group_id
		if (isset($post['form_group']))
		{
			if (is_array($post['form_group']) AND isset($post['form_group']['id']))
			{
				$post['form_group_id'] = $post['form_group']['id'];
			}
			elseif (is_numeric($post['form_group']))
			{
				$post['form_group_id'] = $post['form_group'];
			}
		}
		
		// Load post values into group model
		$attribute->values($post);
		$attribute->id = $id;
		
		// Validation - perform in-model validation before saving
		try
		{
			// Validate base attribute data
			$attribute->check();

			// Validates ... so save
			$attribute->values($post, array(
				'key', 'label', 'input', 'type'
				));
			$attribute->options = ( isset($post['options']) ) ? json_encode($post['options']) : NULL;
			$attribute->save();

			// Response is the complete form
			$this->_response_payload = $attribute->for_api();
		}
		catch (ORM_Validation_Exception $e)
		{
			// @todo throw 400 or similar
			// Error response
			$this->_response_payload = array(
				'errors' => Arr::flatten($e->errors('models'))
				);
		}
	}

	/**
	 * Delete a single attribute
	 * 
	 * DELETE /api/forms/:form_id/attributes/:id
	 * 
	 * @return void
	 */
	public function action_delete_index()
	{
		$id = $this->request->param('id');
		$form_id = $this->request->param('form_id');

		$attribute = ORM::factory('Form_Attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		$this->_response_payload = array();
		if ( $attribute->loaded() )
		{
			// Return the attribute we just deleted (provides some confirmation)
			$this->_response_payload = $attribute->for_api();
			$attribute->delete();
		}
		else
		{
			throw new Http_Exception_404('Attribute does not exist. Attribute ID: \':id\'', array(
				':id' => $id,
			));
		}
	}
}