<?php

/**
 * Ushahidi Form
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\Entity\Form as EntityForm;
use Ushahidi\Core\Ohanzee\StaticEntity;

class Form extends StaticEntity implements EntityForm
{
    const DEFAULT_TYPE = 'report';
    const DEFAULT_REQUIRE_APPROVAL = 1;
    const DEFAULT_EVERYONE_CAN_CREATE = 1;
    const DEFAULT_HIDE_AUTHOR = 0;
    const DEFAULT_HIDE_TIME = 0;
    const DEFAULT_HIDE_LOCATION = 0;
    const DEFAULT_DISABLED = 0;
    const DEFAULT_BASE_LANGUAGE = 'en-US';

    protected $id;
    protected $parent_id;
    protected $name;
    protected $description;
    protected $color;
    protected $type;
    protected $disabled;
    protected $created;
    protected $updated;
    protected $hide_author;
    protected $hide_time;
    protected $hide_location;
    protected $require_approval;
    protected $everyone_can_create;
    protected $targeted_survey;
    protected $can_create;
    protected $tags;

    // StatefulData
    protected function getDefaultData()
    {
        return [
            'type' => 'report',
            'require_approval' => true,
            'everyone_can_create' => true,
            'hide_author' => false,
            'hide_time' => false,
            'hide_location' => false,
            'targeted_survey' => false,
        ];
    }

    // DataTransformer
    protected function getDefinition()
    {
        $typeColor = function ($color) {
            if ($color) {
                return ltrim($color, '#');
            }
        };
        return [
            'id'          => 'int',
            'parent_id'   => 'int',
            'name'        => 'string',
            'description' => 'string',
            'color'       => $typeColor,
            'type'        => 'string',
            'disabled'    => 'bool',
            'created'     => 'int',
            'updated'     => 'int',
            'hide_author'           => 'bool',
            'hide_time'             => 'bool',
            'hide_location'         => 'bool',
            'require_approval'      => 'bool',
            'everyone_can_create'   => 'bool',
            'targeted_survey'       => 'bool',
            'can_create'            => 'array',
            'tags'        => 'array',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'forms';
    }

    // StatefulData
    protected function getImmutable()
    {
        // Hack: Add computed properties to immutable list
        return array_merge(parent::getImmutable(), ['tags', 'can_create']);
    }

    public function __toString()
    {
        return json_encode([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'description' => $this->description
        ]);
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): Form
    {
        if ($action === "update") {
            return new Form([
                "id" => $old_Values['id'],
                "name" => isset($input["name"]) ? $input["name"] : $old_Values['name'],
                "description" => isset($input["description"]) ? $input["description"] : $old_Values['description'],
                "color" => isset($input["color"]) ? $input["color"] : $old_Values['color'],
                "base_language" => isset($input["base_language"])
                ? $input["base_language"]
                : $old_Values['enabled_languages']['default'],
                "type" => isset($input["type"]) ? $input["type"] : $old_Values['type'],
                "disabled" => isset($input["disabled"]) ? $input["disabled"] : $old_Values['disabled'],

                "require_approval" => isset($input["require_approval"])
                ? $input["require_approval"]
                : $old_Values['require_approval'],

                "everyone_can_create" => isset($input["everyone_can_create"])
                ? $input["everyone_can_create"]
                : $old_Values['everyone_can_create'],

                "hide_author" => isset($input["hide_author"])
                ? $input["hide_author"]
                : $old_Values['hide_author'],

                "hide_time" => isset($input["hide_time"])
                ? $input["hide_time"]
                : $old_Values['hide_time'],

                "hide_location" => isset($input["hide_location"])
                ? $input["hide_location"]
                : $old_Values['hide_location'],

                "created" => $old_Values['created'] ?? time(),
                "updated" => time(),

            ]);
        }
        return new Form([
            "name" => $input["name"],
            "description" => isset($input["description"]) ? $input["description"] : null,
            "color" => isset($input["color"]) ? $input["color"] : null,
            "base_language" => isset($input["base_language"]) ? $input["base_language"] : self::DEFAULT_BASE_LANGUAGE,
            "type" => isset($input["type"]) ? $input["type"] : self::DEFAULT_TYPE,
            "disabled" => isset($input["disabled"]) ? $input["disabled"] : self::DEFAULT_DISABLED,

            "require_approval" => isset($input["require_approval"])
            ? $input["require_approval"]
            : self::DEFAULT_REQUIRE_APPROVAL,

            "everyone_can_create" => isset($input["everyone_can_create"])
            ? $input["everyone_can_create"]
            : self::DEFAULT_EVERYONE_CAN_CREATE,

            "hide_author" => isset($input["hide_author"])
            ? $input["hide_author"]
            : self::DEFAULT_HIDE_AUTHOR,

            "hide_time" => isset($input["hide_time"])
            ? $input["hide_time"]
            : self::DEFAULT_HIDE_TIME,

            "hide_location" => isset($input["hide_location"])
            ? $input["hide_location"]
            : self::DEFAULT_HIDE_LOCATION,

            "created" => time(),
            "updated" => time(),
        ]);
    }
}
