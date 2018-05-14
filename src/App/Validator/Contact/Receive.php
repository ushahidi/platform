<?php

/**
 * Ushahidi Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Contact;

use Ushahidi\Core\Entity\Contact;

class Receive extends Create
{
    public function validContact($contact, $data, $validation)
    {
        // Valid Email?
        if (isset($data['type']) and
            $data['type'] == Contact::EMAIL and
             ! \Kohana\Validation\Valid::email($contact) ) {
            return $validation->error('contact', 'invalid_email', [$contact]);
        } elseif (isset($data['type']) and
            $data['type'] == Contact::PHONE ) {
            // Allow for alphanumeric sender
            $number = preg_replace('/[^a-zA-Z0-9 ]/', '', $contact);

            if (strlen($number) == 0) {
                $validation->error('contact', 'invalid_phone', [$contact]);
            }
        }
    }
}
