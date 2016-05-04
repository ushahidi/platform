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

class Ushahidi_Validator_Contact_Receive extends Ushahidi_Validator_Contact_Create
{
	public function valid_contact($contact, $data, $validation)
	{
		// Valid Email?
		if ( isset($data['type']) AND
			$data['type'] == Contact::EMAIL AND
			 ! Valid::email($contact) )
		{
			return $validation->error('contact', 'invalid_email', [$contact]);
		}

		else if ( isset($data['type']) AND
			$data['type'] == Contact::PHONE )
		{
			// Allow for alphanumeric sender
			$number = preg_replace('/[^a-zA-Z0-9 ]/', '', $contact);

			if (strlen($number) == 0)
			{
				$validation->error('contact', 'invalid_phone', [$contact]);
			}
		}
	}
}
