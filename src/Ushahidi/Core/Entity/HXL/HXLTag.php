<?php

/**
 * Ushahidi HXLTag Entity
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\StaticEntity;

class HXLTag extends StaticEntity
{
    protected $id;
    protected $tag_name;
    protected $hxl_attributes;
    protected $form_attribute_types;
    // DataTransformer
    public function getDefinition()
    {
        return [
            'id'        => 'int',
            'tag_name'      => 'string',
            'description' => 'string',
            'hxl_attributes'    => 'array',
            'form_attribute_types' => 'array'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'hxl_tag';
    }
}
