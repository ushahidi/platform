<?php

/**
 * Ushahidi API Formatter for Notifications
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Notification extends API
{
    use FormatterAuthorizerMetadata;

    protected function getFieldName($field)
    {
        $remap = [
            'set_id'  => 'set'
            ];

        if (isset($remap[$field])) {
            return $remap[$field];
        }

        return parent::getFieldName($field);
    }

    protected function formatSetId($set_id)
    {
        return $this->getRelation('sets', $set_id);
    }
}
