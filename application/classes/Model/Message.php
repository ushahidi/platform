<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Messages
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Message extends ORM implements Acl_Resource_Interface {
	/**
	 *
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		);

	/**
	 * A message has zero or one post
	 * @var array Relationships
	 */
	protected $_has_one = array(
	   'post' => array()
		);

	/**
	 * A message belongs to a [parent] data feed
	 * A message belongs to a [parent] contact
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'datafeed' => array(
			'model'  => 'DataFeed',
			'foreign_key' => 'data_feed_id',
			),
		'contact' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	/**
	 * Filters for the Tag model
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
			'title' => array(
				array('trim'),
			),
			'message' => array(
				array('trim'),
			)
		);
	}

	/**
	 * Rules for the tag model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'title' => array(
				array(array($this, 'valid_title'), array(':validation', ':field')),
			),
			'message' => array(
				array('not_empty'),
			),
			'datetime' => array(
				array('date'),
			),
			'type' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
				array('in_array', array(':value', array('sms', 'email', 'twitter')) ),
			),
			'data_provider' => array(
				array('in_array', array(':value', DataProvider::get_available_providers()) ),
			),
			'data_provider_message_id' => array(
				array('max_length', array(':value', 511)),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array('pending', 'received', 'expired', 'cancelled', 'failed', 'unknown', 'archived')) ),
			),
			'direction' => array(
				array('not_empty'),
				array('in_array', array(':value', array('incoming', 'outgoing')) ),
			),
			'parent_id' => array(
				array('numeric'),
				array(array($this, 'parent_exists'), array(':field', ':value'))
			),
			'post_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Post', ':field', ':value'))
			),
			'contact_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Contact', ':field', ':value'))
			),
			'data_feed_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('DataFeed', ':field', ':value'))
			),
		);
	}

	/**
	 * Validate Message Against Message Type
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @param [type] [varname] [description]
	 * @return void
	 */
	public function valid_title($validation, $field)
	{
		// Valid Email?
		if ( isset($validation['type']) AND
			$validation['type'] == 'email' AND ! $validation[$field] )
		{
			$validation->error($field, 'invalid_title');
		}
	}

	/**
	 * Callback function to check if tag parent exists
	 */
	public function parent_exists($field, $value)
	{
		// Skip check if parent is empty
		if (empty($value)) return TRUE;

		$parent = ORM::factory('Message')
			->where('id', '=', $value)
			->where('id', '!=', $this->id)
			->find();

		return $parent->loaded();
	}


	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'messages';
	}

}
