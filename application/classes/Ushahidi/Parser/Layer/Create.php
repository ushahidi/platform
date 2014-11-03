<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Layer Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase\Layer\LayerData;

class Ushahidi_Parser_Layer_Create extends Ushahidi_Parser_Layer_Update
{
	protected function create_data_object($data)
	{
		return new LayerData(
				Arr::extract($data, ['name', 'media_id', 'data_url', 'type', 'active', 'visible_by_default'])
				+ Arr::extract($data, ['options'], [])
			);
	}
}

