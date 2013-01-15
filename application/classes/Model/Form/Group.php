<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Groups
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Form_Group extends ORM {
	/**
	 * A form_group has many groups
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'form_attributes' => array(),
		);

	/**
	 * A form_group belongs to a form
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'form' => array(),
		);

	/**
	 * Prepare group data for API
	 * 
	 * @return array $response - array to be returned by API (as json)
	 */
	public function for_api()
	{
		$response = array();
		if ( $this->loaded() )
		{
			$response = array(
				'url' => url::site('api/v2/forms/'.$this->form_id.'/groups/'.$this->id, Request::current()),
				'form' => url::site('api/v2/forms/'.$this->form_id, Request::current()),
				'id' => $this->id,
				'label' => $this->label,
				'priority' => $this->priority,
				'attributes' => array()
				);
			
			foreach ($this->form_attributes->find_all() as $attribute)
			{
				$response['attributes'][] = $attribute->for_api();
			}
		}
		else
		{
			// @todo throw 404
			$response = array(
				'errors' => array(
					'Group does not exist'
					)
				);
		}

		return $response;
	}
}