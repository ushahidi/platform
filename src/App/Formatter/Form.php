<?php

/**
 * Ushahidi API Formatter for Form
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Form extends API
{
    use FormatterAuthorizerMetadata;

    protected function formatDisabled($value)
    {
        return (bool) $value;
    }

    protected function formatColor($value)
    {
        // enforce a leading hash on color, or null if unset
        $value = ltrim($value, '#');
        return $value ? '#' . $value : null;
    }

    protected function formatTags($tags)
    {
        $output = [];
        foreach ($tags as $tagid) {
            $output[] = $this->getRelation('tags', $tagid);
        }

        return $output;
    }
}
