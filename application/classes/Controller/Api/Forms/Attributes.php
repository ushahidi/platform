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
	 * Retrieve an attribute
	 * 
	 * GET /api/forms/:form_id/attributes/:id
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
				'id' => $attribute->id,
				'key' => $attribute->key,
				'label' => $attribute->label,
				'input' => $attribute->input,
				'type' => $attribute->type,
				'required' => ($attribute->required) ? TRUE : FALSE,
				'default' => $attribute->default,
				'unique' => ($attribute->unique) ? TRUE : FALSE,
				'priority' => $attribute->priority,
				'options' => json_decode($attribute->options)
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