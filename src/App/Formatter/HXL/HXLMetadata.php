<?php

/**
 * Ushahidi API Formatter for HXL Metadata
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter\HXL;

use Ushahidi\App\Formatter\API;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class HXLMetadata extends API
{
    use FormatterAuthorizerMetadata;
}
