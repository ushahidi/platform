<?php

/**
 * Ushahidi API Formatter for User Setting
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter\User;

use Ushahidi\App\Formatter\API;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Setting extends API
{
    use FormatterAuthorizerMetadata;

    protected function formatConfigValueWithFields($value, $fields)
    {
        // If Config Key contains a keywords
        // then we redact some of the information
        // Ideally, we would define some kind of flag for sensitive
        // User setting data to make it more straightforward to identify
        if (strpos($fields['config_key'], 'api') !== false) {
            $value = substr_replace($value, str_repeat('*', strlen($value) - 4), 0, -4);
        }

        return $value;
    }
}
