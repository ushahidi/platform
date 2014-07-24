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
			),
			'datetime' => array(
				function ($value)
				{
					// Try to convert W3C format first
					$date = DateTime::createFromFormat(DateTime::W3C, $value);
					// If that failed, try standard strtotime
					if (! $date)
					{
						$date = date_create($value);
					}

					// Output date in MySQL format
					if ($date)
					{
						$value = $date->format('Y-m-d H:i:s');
					}

					return $value;
				}
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
				array('array_key_exists', array(':value', DataProvider::get_providers()) ),
			),
			'data_provider_message_id' => array(
				array('max_length', array(':value', 511)),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
						Message_Status::PENDING,
						Message_Status::PENDING_POLL,
						Message_Status::ARCHIVED,
						Message_Status::RECEIVED,
						Message_Status::EXPIRED,
						Message_Status::CANCELLED,
						Message_Status::FAILED,
						Message_Status::UNKNOWN,
						Message_Status::SENT
					) ) ),
				array(array($this, 'valid_status'), array(':value', ':original_values'))
			),
			'direction' => array(
				array('not_empty'),
				array('in_array', array(':value', array(Message_Direction::INCOMING, Message_Direction::OUTGOING)) ),
				array(array($this, 'valid_direction'), array(':field', ':value', ':original_values'))
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
			)
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
			$validation['type'] == Message_Type::EMAIL AND ! $validation[$field] )
		{
			$validation->error($field, 'invalid_title');
		}
	}

	/**
	 * Check if status is valid based on message direction
	 * @param  string $value             Status value
	 * @param  array  $original_values   Original model values
	 * @return Boolean
	 */
	public function valid_status($value, $original_values)
	{
		if ($this->direction == Message_Direction::INCOMING)
		{
			// Incoming messages can only be: received, archived
			return in_array($value, array(Message_Status::RECEIVED, Message_Status::ARCHIVED));
		}

		if ($this->direction == Message_Direction::OUTGOING)
		{
			// Outgoing messages can only be: pending, cancelled, failed, unknown, sent
			return in_array($value, array(
				Message_Status::PENDING,
				Message_Status::PENDING_POLL,
				Message_Status::EXPIRED,
				Message_Status::CANCELLED,
				Message_Status::FAILED,
				Message_Status::UNKNOWN,
				Message_Status::SENT
			));
		}
	}

	/**
	 * Check if direction is valid, based on previous value
	 *
	 * @param  [type] $field             Field name
	 * @param  [type] $value             Direction value
	 * @param  array  $original_values   Original model values
	 * @return Boolean
	 */
	public function valid_direction($field, $value, $original_values)
	{
		// Either direction is valid for new post
		if (! $this->loaded()) return TRUE;

		return $value == $original_values[$field];
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
	 * Prepare model data for API
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
				'url' => Ushahidi_Api::url('messages', $this->id),
				'parent' => empty($this->parent_id) ? NULL : array(
					'id' => $this->parent_id,
					'url' => Ushahidi_Api::url('messages', $this->parent_id)
				),
				'contact' => empty($this->contact_id) ? NULL : $this->contact->for_api(),
				'post' => empty($this->post_id) ? NULL : array(
					'id' => $this->post_id,
					'url' => Ushahidi_Api::url('posts', $this->post_id)
				),
				'data_provider' => $this->data_provider,
				'data_provider_message_id' => $this->data_provider_message_id,
				'title' => $this->title,
				'message' => $this->message,
				'datetime' => ($date = DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime))
					? $date->format(DateTime::W3C)
					: $this->datetime,
				'type' => $this->type,
				'status' => $this->status,
				'direction' => $this->direction,
				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
			);
		}
		else
		{
			$response = array(
				'errors' => array(
					'Message does not exist'
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
		return 'messages';
	}

}
