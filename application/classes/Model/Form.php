<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Forms
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Form extends ORM implements Acl_Resource_Interface {
	/**
	 * A form has many groups
	 * A form has and belongs to many attributes
	 * A form has many [children] forms
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'form_groups' => array(),
		'posts' => array(),

		'children' => array(
			'model'  => 'Form',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A form belongs to a user and a [parent] form
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'user' => array(),

		'parent' => array(
			'model'  => 'form',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * Rules for the form model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 150))
			),

			// Form Types
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'report',
					'comment',
					'message',
					'alert'
				)) )
			)
		);
	}

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Prepare form data for API, along with all its
	 * groups and attributes
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
				'url' => Ushahidi_Api::url('forms/', $this->id),
				'name' => $this->name,
				'description' => $this->description,
				'type' => $this->type,
				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
				'updated' => ($updated = DateTime::createFromFormat('U', $this->updated))
					? $updated->format(DateTime::W3C)
					: $this->updated,
				'groups' => array()
				);

			foreach ($this->form_groups->find_all() as $group)
			{
				$response['groups'][] = $group->for_api();
			}
		}
		else
		{
			$response = array(
				'errors' => array(
					'Form does not exist'
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
		return 'forms';
	}
}
