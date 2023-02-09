<?php

/**
 * Ushahidi API Formatter for Form Stage
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Formatter\Form;

use Ushahidi\Modules\V3\Formatter\API;
use Ushahidi\Core\Concerns\FormatterAuthorizerMetadata;

class Stage extends API
{
    use FormatterAuthorizerMetadata;
}
