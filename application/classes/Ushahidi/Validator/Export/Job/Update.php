<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Export Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Export_Job_Update extends Validator
{
	protected $default_error_source = 'export';

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
			'entity_type' => [
				['in_array', [':value', ['post']]],
			]
		];
	}
}
