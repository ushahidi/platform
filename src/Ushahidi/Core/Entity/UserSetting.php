<?php

/**
 * Ushahidi User Setting
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class UserSetting extends StaticEntity
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
}
