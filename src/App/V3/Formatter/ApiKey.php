<?php

/**
 * Ushahidi API Formatter for Api Keys
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Formatter;

use Ushahidi\Core\Concerns\FormatterAuthorizerMetadata;

class ApiKey extends API
{
    use FormatterAuthorizerMetadata;
}