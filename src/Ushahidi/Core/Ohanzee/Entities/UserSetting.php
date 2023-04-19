<?php

/**
 * Ushahidi User Setting
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\StaticEntity;
use Ushahidi\Core\Entity\UserSetting as EntityUserSetting;

class UserSetting extends StaticEntity implements EntityUserSetting
{
    protected $id;
    protected $user_id;
    protected $config_key;
    protected $config_value;
    protected $created;
    protected $updated;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'       => 'int',
            'user_id'  => 'int',
            'config_key'  => 'string',
            'config_value'  => 'string',
            'created'      => 'int',
            'updated'      => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'user_settings';
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): UserSetting
    {
        if ($action === "update") {
            return new UserSetting([
                "id" => $old_Values['id'],
                "user_id" => $old_Values['user_id'],
                "config_key" => isset($input["config_key"]) ? $input["config_key"] : $old_Values['config_key'],
                "config_value" =>
                isset($input["config_value"]) ? $input["config_value"] : $old_Values['config_value'],
                "created" => $old_Values['created'] ?? time(),
                "updated" => time()
            ]);
        }
        return new UserSetting([
            "user_id" => isset($input["user_id"]) ? $input["user_id"] : null,
            "config_key" => $input["config_key"],
            "config_value" => $input["config_value"],
            "created" => time(),
            "updated" => time()

        ]);
    }
}
