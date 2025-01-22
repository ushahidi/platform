<?php

/**
 * Ushahidi Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Tag extends StaticEntity
{
    protected $id;
    protected $parent_id;
    protected $tag;
    protected $slug;
    protected $type;
    protected $color;
    protected $icon;
    protected $description;
    protected $priority;
    protected $created;
    protected $role;
    protected $children;

    // StatefulData
    protected function getDefaultData()
    {
        return [
            'type' => 'category',
            'icon' => 'tag',
            'priority' => 99,
        ];
    }

    // StatefulData
    protected function getDerived()
    {
        return [
            'slug' => 'tag',
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
            'tag'         => 'string',
            'slug'        => '*slug',
            'type'        => 'string',
            'color'       => $typeColor,
            'icon'        => 'string',
            'description' => 'string',
            'priority'    => 'int',
            'created'     => 'int',
            'role'        => '*json',
            'children'    => 'array',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'tags';
    }

    protected function getImmutable()
    {
        // Hack: Add computed properties to immutable list
        return array_merge(parent::getImmutable(), ['children']);
    }
}
