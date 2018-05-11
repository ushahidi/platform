<?php

/**
 * Ushahidi API Formatter for CSV
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use DateTime;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Tos extends API
{
    use FormatterAuthorizerMetadata;

    protected function formatAgreementDate($value)
    {
        return $value ? $value->format(DateTime::W3C) : null;
    }

    protected function formatTosVersionDate($value)
    {
        return $value ? $value->format(DateTime::W3C) : null;
    }
}
