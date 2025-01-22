<?php

/**
 * Ushahidi User Setting Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\User\Setting;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\UserRepository;
use Ushahidi\Contracts\Repository\Entity\UserSettingRepository;

class Update extends LegacyValidator
{
    protected $user_repo;
    protected $user_setting_repo;
    protected $default_error_source = 'user_setting';

    public function __construct(UserRepository $user_repo, UserSettingRepository $user_setting_repo)
    {
        $this->user_repo = $user_repo;
        $this->user_setting_repo = $user_setting_repo;
    }

    protected function getRules()
    {
        return [
            'user_id' => [
                ['digit'],
                [[$this->user_repo, 'exists'], [':value']],
            ],
            'config_key' => [
                ['is_string', [':value']],
                ['min_length', [':value', 3]],
                ['max_length', [':value', 255]]
            ],
            'config_value' => [
                ['is_string', [':value']],
                ['min_length', [':value', 3]],
                ['max_length', [':value', 255]]
            ],
        ];
    }
}
