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

class Ushahidi_Validator_Config_Update implements Validator
{
	protected $valid;

	public function check(Entity $entity)
	{
		$data = $entity->getChanged();
		$keys = array_keys($data);

		$this->valid = Validation::factory($data);

		foreach ($keys as $key)
		{
			// The only restriction on config values is that they must fit within
			// storage constraints, to prevent eg broken JSON strings.
			$this->valid->rules($key, [
				['max_length', [':value', 255]]
			]);
		}

		return $this->valid->check();
	}

	public function errors($from = 'config')
	{
		return $this->valid->errors($from);
	}
}
