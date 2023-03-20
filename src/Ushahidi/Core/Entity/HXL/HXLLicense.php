<?php

/**
 * Ushahidi HXLLicense Entity
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\StaticEntity;

class HXLLicense extends StaticEntity
{
    protected $id;
    protected $code;
    protected $name;
    protected $link;

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id'        => 'int',
            'code'      => 'string',
            'name'      => 'string',
            'link'      => 'string',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'hxl_license';
    }
}
