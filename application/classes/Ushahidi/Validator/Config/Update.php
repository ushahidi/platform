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
	protected $default_error_source = 'config';

	protected function getRules()
	{
		$data = $this->validation_engine->getData();
		$config_group = isset($data['id']) ? $data['id'] : false;

		switch($config_group) {
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

			case 'data-provider':
				$rules = [
					'providers' => [
						[[$this, 'isProviderAvailable'], [':value', ':validation']]
					]
				];
				break;

			default:
				$rules = [];
				break;
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

	public function isProviderAvailable($enabled_providers, $validation)
	{
		if ($enabled_providers != null) {
			$enabled_providers = array_filter($enabled_providers);
			$available_providers = array_filter(\Kohana::$config->load('features.data-providers'));

			$diff = array_diff_key($enabled_providers, $available_providers);
			if ($diff) {
				$validation->error('providers', 'providerNotAvailable', [$diff]);
			}
		}
	}
}
