<?php

/**
 * Ushahidi CountryCode Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class CountryCode extends StaticEntity
{
    protected $id;
    protected $country_name;
    protected $dial_code;
    protected $country_code;

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id'           => 'int',
            'country_name' => 'string',
            'dial_code'    => 'string',
            'country_code' => 'string'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'country_codes';
    }
}
