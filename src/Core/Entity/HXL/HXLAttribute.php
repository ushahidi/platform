<?php

/**
 * Ushahidi HXLAttribute Entity
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\StaticEntity;

class HXLAttribute extends StaticEntity
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
            'attribute'      => 'string',
            'description' => 'string',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'hxl_attribute';
    }
}
