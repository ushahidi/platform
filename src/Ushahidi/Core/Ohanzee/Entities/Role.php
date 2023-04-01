<?php

/**
 * Ushahidi Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\Ohanzee\StaticEntity;
use Ushahidi\Core\Entity\Role as EntityRole;

class Role extends StaticEntity implements EntityRole
{
    const DEFAULT_PROTECTED = 0;

    protected $id;
    protected $name;
    protected $display_name;
    protected $description;
    protected $permissions;
    protected $protected;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'           => 'int',
            'name'         => 'string',
            'display_name' => 'string',
            'description'  => 'string',
            'permissions'  => 'array',
            'protected'    => 'boolean',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'roles';
    }

    // Entity
    public function getId()
    {
        return $this->name;
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['name','protected']);
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): Role
    {
        if ($action === "update") {
            return new Role([
                "id" => $old_Values['id'],
                "name" => isset($input["name"]) ? $input["name"] : $old_Values['name'],
                "display_name" =>
                isset($input["display_name"]) ? $input["display_name"] : $old_Values['display_name'],
                "description" => isset($input["description"]) ? $input["description"] : $old_Values['description'],
                "protected" =>  $old_Values['protected'], // protected can't be changed
                "created" => $old_Values['created'] ?? time(),
                "updated" => time()
            ]);
        }
        return new Role([
            "name" => $input["name"],
            "display_name" => $input["display_name"],
            "description" => isset($input["description"]) ? $input["description"] : null,
            "protected" => isset($input["protected"]) ? $input["protected"] : self::DEFAULT_PROTECTED,
            "created" => time()
        ]);
    }
}
