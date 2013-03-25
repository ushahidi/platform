<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Forms
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

class Model_Form extends ORM {
	/**
	 * A form has many attributes, groups, and posts
	 * A form has many [children] forms
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'form_attributes' => array(),
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
	 * @var array Relationhips
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
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

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
				'url' => url::site('api/v2/forms/'.$this->id, Request::current()),
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
}
