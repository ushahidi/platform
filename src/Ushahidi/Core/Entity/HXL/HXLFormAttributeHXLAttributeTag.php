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

class HXLFormAttributeHXLAttributeTag extends StaticEntity
{
    protected $id;
    protected $form_attribute_id;
    protected $hxl_attribute_id;
    protected $hxl_tag_id;
    protected $export_job_id;
    // DataTransformer
    public function getDefinition()
    {
        return [
            'id'        => 'int',
            'form_attribute_id'      => 'int',
            'hxl_attribute_id' => 'int',
            'hxl_tag_id'    => 'int',
            'export_job_id' => 'int'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'form_attribute_hxl_attribute_tag';
    }
}
