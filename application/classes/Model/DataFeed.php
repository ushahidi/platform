<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Data Feeds
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_DataFeed extends ORM implements Acl_Resource_Interface {
	/**
	 * A data feed has many messasges
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'messages' => array(),
		);

	/**
	 * A tag belongs to a [parent] user
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array();


	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	// Table Name
	protected $_table_name = 'data_feeds';

	// Serialized columns, stored as json
	protected $_serialize_columns = array('options');

	/**
	 * Filters for the Tag model
	 *
	 * @return array Filters
	 */
	public function filters()
	{
		return array(
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
			'data_provider' => array(
				array('in_array', array(':value', DataProvider::get_available_providers()) ),
			),
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 150)),
			),
		);
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'datafeeds';
	}

}
