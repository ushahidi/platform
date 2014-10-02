<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update Layer Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Exception\ParserException;
use Ushahidi\Usecase\Layer\LayerData;

class Ushahidi_Parser_Layer_Update implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('active', array(
					array('in_array', [':value', [TRUE, FALSE, 0, 1], TRUE]),
				))
			->rules('visible_by_default', array(
					array('in_array', [':value', [TRUE, FALSE, 0, 1], TRUE]),
				));

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse layer update request", $valid->errors('layer'));
		}

		if (is_bool($data['active'])) {
			$data['active'] = (int) $data['active'];
		}
		if (is_bool($data['visible_by_default'])) {
			$data['visible_by_default'] = (int) $data['visible_by_default'];
		}

		return $this->create_data_object($data);
	}

	protected function create_data_object($data)
	{
		return new LayerData($data);
	}
}

