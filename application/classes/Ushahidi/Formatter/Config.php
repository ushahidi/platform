<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Config extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	protected $config_group = null;

	public function __invoke($entity)
	{
		if ($entity && isset($entity->id)) {
			$this->config_group = $entity->id;
		}

		return parent::__invoke($entity);
	}

	protected function format_clustering($val)
	{
		if ($this->config_group == 'map') {
			return (bool) $val;
		}

		return $val;
	}

	protected function format_cluster_radius($val)
	{
		if ($this->config_group == 'map') {
			return (integer) $val;
		}

		return $val;
	}
}
