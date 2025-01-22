<?php

/**
 * Ushahidi User Login Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\User;

use Ushahidi\Modules\V3\Validator\LegacyValidator;

class Login extends LegacyValidator
{
    protected $default_error_source = 'user';

    protected function getRules()
    {
        return [
            'email' => [
                ['not_empty'],
            ],
            'password' => [
                ['not_empty'],
                // No reason to validate length here, even though the password
                // is plaintext, because we always want to run the hash check.
            ],
        ];
    }
}
