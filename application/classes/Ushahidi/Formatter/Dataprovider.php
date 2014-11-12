<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Dataprovider extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	protected function format_options(Array $options)
	{
		foreach ($options as $name => $input)
		{
			if (isset($input['description']) AND $input['description'] instanceof \Closure)
			{
				$options[$name]['description'] = $options[$name]['description']();
			}

			if (isset($input['label']) AND $input['label'] instanceof \Closure)
			{
				$options[$name]['label'] = $options[$name]['label']();
			}

			if (isset($input['rules']) AND $input['rules'] instanceof \Closure)
			{
				$options[$name]['rules'] = $options[$name]['rules']();
			}
		}
		return $options;
	}
}
