<?php

/**
 * Ushahidi Contact Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Contact;

use Ushahidi\Contracts\Contact;
use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

class Update extends LegacyValidator
{
    protected $user_repo;
    protected $default_error_source = 'contact';

    public function __construct(UserRepository $repo)
    {
        $this->user_repo = $repo;
    }

    protected function getRules()
    {
        // @todo inject
        $sources = app('datasources');

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
                ['in_array', [':value', [Contact::EMAIL, Contact::PHONE, Contact::WHATSAPP, Contact::TWITTER]]],
            ],
            'data_source' => [
                ['in_array', [':value', array_keys($sources->getEnabledSources())]],
            ],
            'contact' => [
                ['max_length', [':value', 255]],
                [[$this, 'validContact'], [':value', ':data', ':validation']],
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
    public function validContact($contact, $data, $validation)
    {
        // Valid Email?
        if (isset($data['type']) and
            $data['type'] == Contact::EMAIL and
             ! \Kohana\Validation\Valid::email($contact)) {
            return $validation->error('contact', 'invalid_email', [$contact]);
        // Valid Phone?
        // @todo Look at using libphonenumber to validate international numbers
        } elseif (isset($data['type']) and
            $data['type'] == Contact::PHONE) {
            // Remove all non-digit characters from the number
            $number = preg_replace('/\D+/', '', $contact);

            if (strlen($number) == 0) {
                $validation->error('contact', 'invalid_phone', [$contact]);
            }
        }
    }
}
