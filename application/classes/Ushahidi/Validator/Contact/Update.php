<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;

class Ushahidi_Validator_Contact_Update extends Validator
{
	protected $user_repo;
	protected $default_error_source = 'contact';

	public function __construct(UserRepository $repo)
	{
		$this->user_repo = $repo;
	}

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
			'user_id' => [
				[[$this->user_repo, 'exists'], [':value']],
			],
			'type' => [
				['max_length', [':value', 255]],
				// @todo this should be shared via repo or other means
				['in_array', [':value', [Contact::EMAIL, Contact::PHONE, Contact::TWITTER]]],
			],
			'data_provider' => [
				// @todo DataProvider should provide a list of available types
				['in_array', [':value', array_keys(\DataProvider::get_providers())]],
			],
			'contact' => [
				['max_length', [':value', 255]],
				[[$this, 'valid_contact'], [':value', ':data', ':validation']],
			]
		];
	}

	/**
	 * Validate Contact Against Contact Type
	 *
	 * @param array $validation
	 * @param string $field field name
	 * @param [type] [varname] [description]
	 * @return void
	 */
	public function valid_contact($contact, $data, $validation)
	{
		// Valid Email?
		if ( isset($data['type']) AND
			$data['type'] == Contact::EMAIL AND
			 ! Valid::email($contact) )
		{
			return $validation->error('contact', 'invalid_email', [$contact]);
		}

		// Valid Phone?
		// @todo Look at using libphonenumber to validate international numbers
		else if ( isset($data['type']) AND
			$data['type'] == Contact::PHONE )
		{
			// Remove all non-digit characters from the number
			$number = preg_replace('/\D+/', '', $contact);

			if (strlen($number) == 0)
			{
				$validation->error('contact', 'invalid_phone', [$contact]);
			}
		}
	}
}
