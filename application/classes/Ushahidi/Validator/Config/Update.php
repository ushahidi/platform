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
use Ushahidi\Core\Entity\ConfigRepository;

class Ushahidi_Validator_Config_Update extends Validator
{
	protected $config_group = null;
	protected $default_error_source = 'config';

	// Snoop the config group, then pass it on.
	public function check(array $data)
	{
		if (isset($data['id'])) {
			$this->config_group = $data['id'];
		}

		return parent::check($data);
	}

	protected function getRules()
	{
		$rules = [];

		if ($this->config_group) {
			switch($this->config_group) {
				case 'site':
					$rules = [
						'name' => [
							['is_string', [':value']],
							['min_length', [':value', 3]],
							['max_length', [':value', 255]]
						],
						'email' => [
							['email', [':value']],
							['max_length', [':value', 150]]
						],
						'language' => [
							['is_string', [':value']],
							['min_length', [':value', 2]],
							['max_length', [':value', 5]]
						],
						'timezone' => [
							['is_string', [':value']],
							['min_length', [':value', 3]],
							['max_length', [':value', 255]],
							[[$this, 'validTimeZone'], [':value']]
						],
						'date_format' => [
							['is_string', [':value']],
							['max_length', [':value', 5]],
							['regex', [':value', '/^[a-zA-Z]\/[a-zA-Z]\/[a-zA-Z]$/i']]
						]
					];
					break;

				case 'map':
					$rules = [
						'cluster_radius' => [
							['digit', [':value']]
						],
						'clustering' => [
							['in_array', [':value', [0, 1, false, true], TRUE]]
						],
						'default_view' => [
							['is_array', [':value']]
						]
					];
					break;
			}
		}

		return $rules;
	}

	public function validTimeZone($string)
	{
		if ($string) {
			try {
				$check = new DateTimeZone($string);
			} catch (\Exception $e) {
				$check = false;
			}

			if ($check !== false) {
				return true;
			}
		}

		return false;
	}
}
