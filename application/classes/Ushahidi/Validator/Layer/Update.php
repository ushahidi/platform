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
use Ushahidi\Core\Usecase\Layer\LayerMediaRepository;

class Ushahidi_Validator_Layer_Update extends Validator
{
	protected $media;
	protected $default_error_source = 'layer';

	public function setMedia(LayerMediaRepository $media)
	{
		$this->media = $media;
	}

	protected function getRules()
	{
		return [
			'name' => [
				['min_length', [':value', 2]],
				['max_length', [':value', 50]],
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			],
			'data_url' => [
				['url']
			],
			'type' => [
				['in_array', [':value', ['geojson', 'wms', 'tile']]],
			],
			'active' => [
				['in_array', [':value', [0, 1, false, true], TRUE]],
			],
			'visible_by_default' => [
				['in_array', [':value', [0, 1, false, true], TRUE]],
			],
			'media_id' => [
				[[$this->media, 'doesMediaExist'], [':value']],
			],
			'options' => [
				['is_array', [':value']],
			],
		];
	}
}
