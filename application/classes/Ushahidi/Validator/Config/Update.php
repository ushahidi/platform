<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Config Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Config_Update extends Validator
{
	protected $default_error_source = 'config';

	protected function getRules()
	{
		// The only restriction on config values is that they must fit within
		// storage constraints, to prevent eg broken JSON strings.
		return array_fill_keys(array_keys($this->validation_engine->getData()), [
			['max_length', [':value', 255]],
		]);
	}
}
