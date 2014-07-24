<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Contact extends ORM implements Acl_Resource_Interface {

	const EMAIL = 'email';
	const PHONE = 'phone';
	const TWITTER = 'twitter';

	/**
	 * A contact has many messages
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'messages' => array(),
		);

	/**
	 * A contact belongs to a [parent] user
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'user' => array(),
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
			'contact' => array(
				array('UTF8::strtolower'),
				array(array($this, 'clean_phone_number'), array(':value'))
			),
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
			'user_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('User', ':field', ':value'))
			),
			'data_provider' => array(
				array('array_key_exists', array(':value', DataProvider::get_providers()) ),
			),
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(self::EMAIL, self::PHONE, self::TWITTER)) ),
			),
			'contact' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
				array(array($this, 'valid_contact'), array(':validation', ':field')),
			),
		);
	}

	/**
	 * Validate Contact Against Contact Type
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @param [type] [varname] [description]
	 * @return void
	 */
	public function valid_contact($validation, $field)
	{
		// Valid Email?
		if ( isset($validation['type']) AND
			$validation['type'] == self::EMAIL AND
			 ! Valid::email($validation[$field]) )
		{
			$validation->error($field, 'invalid_email');
		}

		// Valid Phone?
		// ++TODO: There's no easy to validate international numbers
		// so just look for numbers only. A valid international phone
		// number should have atleast 9 digits
		else if ( isset($validation['type']) AND
			$validation['type'] == self::PHONE )
		{
			// Remove all non-digit characters from the number
			$number = preg_replace('/\D+/', '', $validation[$field]);

			if (strlen($number) < 9)
			{
				$validation->error($field, 'invalid_phone');
			}
		}
		else
		{
			if ( ! $validation[$field])
			{
				$validation->error($field, 'invalid_account');
			}
		}
	}

	/**
	 * Finds and returns the Contact record associated with
	 * the specified contact and contact type
	 *
	 * @param string  contact
	 * @param stirng  contact_type
	 *
	 * @return Model_Contact if found, FALSE otherwise
	 */
	public static function get_contact($contact, $contact_type)
	{
		$contact = ORM::factory('Contact')
		    ->where('contact', '=', $contact)
		    ->where('type', '=', $contact_type)
		    ->find();

		return $contact->loaded() ? $contact : FALSE;
	}

	public function clean_phone_number($value)
	{
		// Clean up phone numbers
		if ($this->type == self::PHONE)
		{
			$value = preg_replace("/[^0-9,.]/", "", $value);
		}

		return $value;
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'contacts';
	}

	/**
	 * Prepare data for API
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
				'url' => Ushahidi_Api::url('contacts', $this->id),
				'user' => empty($this->user_id) ? NULL : array(
					'id' => $this->user_id,
					'url' => Ushahidi_Api::url('users', $this->user_id)
				),
				'contact' => $this->contact,
				'type' => $this->type,
				'data_provider' => $this->data_provider,
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

}
