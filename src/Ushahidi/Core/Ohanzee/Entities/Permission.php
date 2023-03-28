<?php

/**
 * Ushahidi Permission Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\Entity\Permission as EntityPermission;
use Ushahidi\Core\Ohanzee\StaticEntity;

class Permission extends StaticEntity implements EntityPermission
{
    protected $id;
    protected $name;
    protected $description;

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'description' => 'string',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'permission';
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['name']);
    }
}
