<?php

/**
 * Ushahidi API Formatter for HXL License
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https=>//www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Formatter\HXL;

use Ushahidi\Contracts\Formatter;
use Ushahidi\Core\Concerns\FormatterAuthorizerMetadata;

class HXLOrganisations implements Formatter
{
    use FormatterAuthorizerMetadata;

    /**
     * @param  mixed $input
     * @return mixed
     * @throws \Ushahidi\Core\Exception\FormatterException
     */
    public function __invoke($input)
    {
        return $input;
    }
}
