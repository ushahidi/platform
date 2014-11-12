<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Groups
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Form_Group extends ORM implements Acl_Resource_Interface {
	/**
	 * A form_group has and belongs to many attributes
	 *
	 * @var array Relationships
	 */
	protected $_has_many = [
		'form_attributes' => [
			'model' => 'Form_Attribute',
		]
	];

	/**
	 * A form_group belongs to a form
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'form' => array(),
		);

	/**
	 * Rules for the form_attribute model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'form_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Form', ':field', ':value'))
			),
			'label' => array(
				array('not_empty'),
				array('max_length', array(':value', 150))
			),
			'priority' => array(
				array('numeric')
			),
		);
	}

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
				'id' => $this->id,
				'url' => Ushahidi_Api::url('forms/'.$this->form_id.'/groups', $this->id),
				'form' => empty($this->form_id) ? NULL : array(
					'url' => Ushahidi_Api::url('forms', $this->form_id),
					'id' => $this->form_id
				),
				'label' => $this->label,
				'priority' => $this->priority,
				'icon' => $this->icon,
				'attributes' => array()
				);

			foreach ($this->form_attributes->find_all() as $attribute)
			{
				$response['attributes'][] = $attribute->for_api();
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

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'form_groups';
	}
}
