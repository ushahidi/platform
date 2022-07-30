<?php

/**
 * Ushahidi API Formatter for Form Stats
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Formatter\Form;

use Ushahidi\App\V3\Formatter\API;
use Ushahidi\Core\Concerns\FormatterAuthorizerMetadata;

class Stats extends API
{
    use FormatterAuthorizerMetadata;
}
