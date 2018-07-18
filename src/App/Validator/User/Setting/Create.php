<?php

/**
 * Ushahidi User Setting Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\User\Setting;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;

class Create extends Update
{
    protected function getRules()
    {
        return array_merge_recursive(parent::getRules(), [
            'config_key' => [
                [[$this, 'isUserConfigKeyPairUnique'], [':validation', ':data', ':value']]
            ],
        ]);
    }

    public function isUserConfigKeyPairUnique($validation, $data, $config_key)
    {
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;
        if ($user_id && $this->user_setting_repo->userConfigKeyPairExists($user_id, $config_key)) {
            $validation->error('config_key', 'duplicateConfigKeyUser', [$user_id, $config_key]);
        }
    }
}
