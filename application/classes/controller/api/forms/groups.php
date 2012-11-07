<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Forms Groups Controller
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

class Controller_API_Forms_Groups extends Ushahidi_API {

	/**
	 * Retrieve a group
	 * 
	 * GET /api/forms/:form_id/groups/:id
	 * 
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$form_id = $this->request->param('form_id');
		$results = array();

		$groups = ORM::factory('form_group')
			->order_by('id', 'ASC')
			->where('form_id', '=', $form_id)
			->find_all();

		$count = $groups->count();

		foreach ($groups as $group)
		{
			$results[] = $this->group($group);
		}

		// Respond with groups
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
			);
	}

	/**
	 * Retrieve a single group, along with all its attributes
	 * 
	 * @param $group object - group model
	 * @return array $response
	 */
	public static function group($group = NULL)
	{
		$response = array();
		if ( $group->loaded() )
		{
			$response = array(
				'url' => url::site('api/v2/forms/'.$group->form_id.'/groups/'.$group->id, Request::current()),
				'id' => $group->id,
				'label' => $group->label,
				'priority' => $group->priority,
				'attributes' => array()
				);
			
			foreach ($group->form_attributes->find_all() as $attribute)
			{
				$response['attributes'][] = Controller_API_Forms_Attributes::attribute($attribute);
			}
		}
		else
		{
			$response = array(
				'errors' => array(
					'Group does not exist'
					)
				);
		}

		return $response;
	}
}