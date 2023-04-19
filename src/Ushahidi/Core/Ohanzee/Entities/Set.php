<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\StaticEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Set as EntitySet;

class Set extends StaticEntity implements EntitySet
{
    protected $id;
    protected $user_id;
    protected $name;
    protected $description;
    protected $url;
    protected $view;
    protected $view_options;
    protected $role;
    protected $featured;
    protected $created;
    protected $updated;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'           => 'int',
            'user_id'      => 'int',
            'name'         => 'string',
            'description'  => 'string',
            'url'          => '*url',
            'view'         => 'string',
            'view_options' => '*json',
            'role'   => '*json',
            'featured'     => 'boolean',
            'created'      => 'int',
            'updated'      => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'sets';
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['user_id']);
    }

    // StatefulData
    protected function getDerived()
    {
        return [
            'user_id'   => ['user', 'user.id'], /* alias */
        ];
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null)
    {

        if ($action === "update") {
            return new Set([
                "id" => $old_Values['id'],
                "user_id" => isset($input["user_id"]) ? $input["user_id"] : $old_Values['user_id'],
                "name" => isset($input["name"]) ? $input["name"] : $old_Values['name'],
                "description" => isset($input["description"]) ? $input["description"] : $old_Values['description'],
                "view" => isset($input["view"]) ? $input["view"] : $old_Values['view'],
                "view_options" => isset($input["view_options"]) ? $input["view_options"] : $old_Values['view_options'],
                "role" => isset($input["role"]) ? $input["role"] : $old_Values['role'],
                "featured" => isset($input["featured"]) ? $input["featured"] : $old_Values['featured'],
                "created" => $old_Values['created'] ?? time(),
                "updated" => time()
            ]);
        }
        return new Set([
            "user_id" => isset($input["user_id"]) ? $input["user_id"] : Auth::id(),
            "name" => isset($input["name"]) ? $input["name"] : '',
            "description" => isset($input["description"]) ? $input["description"] : '',
            "view" => isset($input["view"]) ? $input["view"] : self::DEFAULT_VIEW,
            "view_options" => isset($input["view_options"]) ? $input["view_options"] : null,
            "role" => isset($input["role"]) ? $input["role"] : null,
            "featured" => isset($input["featured"]) ? $input["featured"] : self::DEFAULT_FEATURED,
            "created" => time(),
            "updated" => time()
        ]);
    }

}
