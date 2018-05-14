<?php

/**
 * Ushahidi API Formatter for Layer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Layer extends API
{
    use FormatterAuthorizerMetadata;

    protected function getFieldName($field)
    {
        $remap = [
            'media_id' => 'media',
            ];

        if (isset($remap[$field])) {
            return $remap[$field];
        }

        return parent::getFieldName($field);
    }

    protected function formatMediaId($media_id)
    {
        return $this->getRelation('media', $media_id);
    }
}
