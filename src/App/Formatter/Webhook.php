<?php

/**
 * Ushahidi API Formatter for Webhooks
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Webhook extends API
{
    use FormatterAuthorizerMetadata;

    protected function getFieldName($field)
    {
        $remap = [
            'user_id'  => 'user'
            ];

        if (isset($remap[$field])) {
            return $remap[$field];
        }

        return parent::getFieldName($field);
    }

    protected function formatUserId($user_id)
    {
        return $this->getRelation('users', $user_id);
    }
}
