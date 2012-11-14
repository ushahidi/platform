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
		
		$form = ORM::factory('form', $form_id);
		
		if ( ! $form->loaded())
		{
			$this->_response_payload = array(
				'errors' => array(
					'Form does not exist'
					)
				);
			return;
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
		
		$group = ORM::factory('form_group', $post["form_group_id"]);
		
		if ( ! $group->loaded())
		{
			$this->_response_payload = array(
				'errors' => array(
					'Group does not exist'
					)
				);
			return;
		}
		
		$attribute = ORM::factory('form_attribute')->values($post);
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
			$this->_response_payload = $this->attribute($attribute);
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

		$attributes = ORM::factory('form_attribute')
			->order_by('id', 'ASC')
			->where('form_id', '=', $form_id)
			->find_all();

		$count = $attributes->count();

		foreach ($attributes as $attribute)
		{
			$results[] = $this->attribute($attribute);
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

		$attribute = ORM::factory('form_attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		$this->_response_payload = $this->attribute($attribute);
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

		$attribute = ORM::factory('form_attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();
		
		if ( ! $attribute->loaded())
		{
			$this->_response_payload = array(
				'errors' => array(
					'Attribute does not exist'
					)
				);
			return;
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
			$this->_response_payload = $this->attribute($attribute);
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

		$attribute = ORM::factory('form_attribute')
			->where('form_id', '=', $form_id)
			->where('id', '=', $id)
			->find();

		$this->_response_payload = array();
		if ( $attribute->loaded() )
		{
			// Return the attribute we just deleted (provides some confirmation)
			$this->_response_payload = $this->attribute($attribute);
			$attribute->delete();
		}
	}

	/**
	 * Retrieve a single attribute
	 * 
	 * @param $attribute object - attribute model
	 * @return array $response
	 */
	public static function attribute($attribute = NULL)
	{
		$response = array();
		if ( $attribute->loaded() )
		{
			$response = array(
				'url' => url::site('api/v2/forms/'.$attribute->form_id.'/attributes/'.$attribute->id, Request::current()),
				'form' => url::site('api/v2/forms/'.$attribute->form_id, Request::current()),
				'form_group' => url::site('api/v2/forms/'.$attribute->form_id.'/groups/'.$attribute->form_group_id, Request::current()),
				'id' => $attribute->id,
				'key' => $attribute->key,
				'label' => $attribute->label,
				'input' => $attribute->input,
				'type' => $attribute->type,
				'required' => ($attribute->required) ? TRUE : FALSE,
				'default' => $attribute->default,
				'unique' => ($attribute->unique) ? TRUE : FALSE,
				'priority' => $attribute->priority,
				'options' => json_decode($attribute->options),
			);
		}
		else
		{
			$response = array(
				'errors' => array(
					'Attribute does not exist'
					)
				);
		}

		return $response;
	}
}