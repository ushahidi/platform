<?php

/**
 * Ushahidi User Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\User;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

class Reset extends LegacyValidator
{

    protected $default_error_source = 'user';
    protected $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    protected function getRules()
    {
        return [
            'token' => [
                [[$this, 'checkResetToken'], [':validation', ':value']]
            ],
            'password' => [
                ['min_length', [':value', 7]],
                ['max_length', [':value', 72]],
            ],
        ];
    }

    public function checkResetToken(\Kohana\Validation\Validation $validation, $token)
    {
        $token = is_base64($token) ? base64_decode($token, true) : $token;

        if (!$this->repo->isValidResetToken($token)) {
            $validation->error('token', 'invalidResetToken');
        }
    }
}
