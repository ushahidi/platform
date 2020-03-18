<?php

/**
 * Ushahidi API Formatter for Export Jobs
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;
use Ushahidi\Core\Traits\FormatRackspaceURL;

class ExportJob extends API
{
    use FormatterAuthorizerMetadata;
    use FormatRackspaceURL;
}
