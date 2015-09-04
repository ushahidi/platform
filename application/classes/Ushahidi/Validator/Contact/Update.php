<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\ContactRepository;

class Ushahidi_Validator_Contact_Update extends Validator
{
	protected $repo;
	protected $default_error_source = 'contact';

	public function __construct(ContactRepository $repo)
	{
		$this->repo = $repo;
	}

	protected function getRules()
	{
		return [
			'id' => [
				[[$this->repo, 'exists'], [':value']],
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
		// ++TODO: There's no easy to validate international numbers
		// so just look for numbers only. A valid international phone
		// number should have atleast 9 digits
		else if ( isset($data['type']) AND
			$data['type'] == Contact::PHONE )
		{
			// Remove all non-digit characters from the number
			$number = preg_replace('/\D+/', '', $contact);

			if (strlen($number) < 0)
			{
				$validation->error('contact', 'invalid_phone', [$contact]);
			}
		}
		else
		{
			if ( ! $validation['contact'])
			{
				$validation->error('contact', 'invalid_account');
			}
		}
	}
}
