<?php

/**
 * Ushahidi API Formatter for Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Dataprovider extends API
{
    use FormatterAuthorizerMetadata;

    protected function formatOptions(array $options)
    {
        foreach ($options as $name => $input) {
            if (isset($input['description']) and $input['description'] instanceof \Closure) {
                $options[$name]['description'] = $options[$name]['description']();
            }

            if (isset($input['label']) and $input['label'] instanceof \Closure) {
                $options[$name]['label'] = $options[$name]['label']();
            }

            if (isset($input['rules']) and $input['rules'] instanceof \Closure) {
                $options[$name]['rules'] = $options[$name]['rules']();
            }
        }
        return $options;
    }
}
