<?php

/**
 * Ushahidi ApiKey Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\ApiKey;

use Ushahidi\Modules\V3\Validator\LegacyValidator;

class Update extends LegacyValidator
{
    protected $default_error_source = 'apikey';

    protected function getRules()
    {
        return [

        ];
    }
}
