<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Layer_Create extends Ushahidi_Validator_Layer_Update
{
	protected $default_error_source = 'layer';

	protected function getRules()
	{
		return array_merge_recursive(parent::getRules(), [
			'name' => [
				['not_empty'],
			],
			'type' => [
				['not_empty'],
			],
			'active' => [
				['not_empty'],
			],
			'visible_by_default' => [
				['not_empty'],
			],
			'data_url' => [
				[function($validation, $data) {
					// At least 1 of data_url and media_id must be defined..
					if (empty($data['data_url']) AND empty($data['media_id']))
					{
						$validation->error('data_url', 'dataUrlOrMediaRequired');
					}
					// .. but both can't be defined at the same time
					if (! empty($data['data_url']) AND ! empty($data['media_id']))
					{
						$validation->error('data_url', 'dataUrlMediaConflict');
					}
				}, [':validation', ':data']]
			],
		]);
	}
}
