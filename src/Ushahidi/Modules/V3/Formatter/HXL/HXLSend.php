<?php

/**
 * Ushahidi API Formatter for HXL License
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Formatter\HXL;

use Ushahidi\Modules\V3\Formatter\API;
use Ushahidi\Core\Concerns\FormatterAuthorizerMetadata;

class HXLSend extends API
{
    use FormatterAuthorizerMetadata;

    /**
     * @param $job
     * @return array|mixed|\Ushahidi\Modules\V3\Formatter\Array
     */
    public function __invoke($job)
    {
        // TODO graceful error reporting to the client
        return $job->asArray();
    }
}
