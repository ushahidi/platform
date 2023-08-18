<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\StaticEntity;

class Set extends StaticEntity
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
            'id' => 'int',
            'user_id' => 'int',
            'name' => 'string',
            'description' => 'string',
            'url' => '*url',
            'view' => 'string',
            'view_options' => '*json',
            'role' => '*json',
            'featured' => 'boolean',
            'created' => 'int',
            'updated' => 'int'
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
            'user_id' => ['user', 'user.id'] /* alias */
        ];
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): Set
    {

        if ($action === "update") {
            //dd($input["role"]);
            //if (array_key_exists("role",$input))
            return new Set([
                "id" => $old_Values['id'],
                "user_id" => array_key_exists("user_id", $input) ? $input["user_id"] : $old_Values['user_id'],
                "name" => array_key_exists("name", $input) ? $input["name"] : $old_Values['name'],
                "description" =>
                array_key_exists("description", $input)
                ? $input["description"]
                : $old_Values['description'],
                "view" => array_key_exists("view", $input) ? $input["view"] : $old_Values['view'],
                "view_options" =>
                array_key_exists("view_options", $input)
                ? $input["view_options"]
                : $old_Values['view_options'],
                "role" => array_key_exists("role", $input) ? $input["role"] : $old_Values['role'],
                "featured" => array_key_exists("featured", $input) ? $input["featured"] : $old_Values['featured'],
                "created" => $old_Values['created'] ? strtotime($old_Values['created']): time(),
                "updated" => time()
            ]);
        }
        return new Set([
            "user_id" => array_key_exists("user_id", $input) ? $input["user_id"] : Auth::id(),
            "name" => array_key_exists("name", $input) ? $input["name"] : '',
            "description" => array_key_exists("description", $input) ? $input["description"] : '',
            "view" => array_key_exists("view", $input) ? $input["view"] : self::DEFAULT_VIEW,
            "view_options" => array_key_exists("view_options", $input) ? $input["view_options"] : null,
            "role" => array_key_exists("role", $input) ? $input["role"] : null,
            "featured" => array_key_exists("featured", $input) ? $input["featured"] : self::DEFAULT_FEATURED,
            "created" => time(),
            "updated" => time()
        ]);
    }
}
