<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Permission Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Permission_Update extends Validator
{
	protected $default_error_source = 'permission';

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
		];
	}
}
