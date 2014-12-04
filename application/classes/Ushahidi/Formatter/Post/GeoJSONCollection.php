<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
use Symm\Gisconverter\Decoders\WKT;
use Symm\Gisconverter\Exceptions\InvalidText;

use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\Formatter\GeoJSONFormatter;
use Ushahidi\Core\SearchData;

class Ushahidi_Formatter_Post_GeoJSONCollection implements Formatter
{
	use GeoJSONFormatter;

	// Formatter
	public function __invoke($entities)
	{
		if (!is_array($entities)) {
			throw new FormatterException('Collection formatter requries an array of entities');
		}

		$output = [
			'type' => 'FeatureCollection',
			'features' => []
		];

		foreach ($entities as $entity)
		{
			$geometries = [];
			foreach($entity->values as $value)
			{
				if ($value->type !== 'point' AND $value->type !== 'geometry')
				{
					continue;
				}

				if ($geometry = $this->valueToGeometry($value->value, $value->type))
				{
					$geometries[] = $geometry;
				}
			}

			if (! empty($geometries))
			{
				$output['features'][] = [
					'type' => 'Feature',
					'geometry' => [
						'type' => 'GeometryCollection',
						'geometries' => $geometries
					],
					'properties' => [
						'title' => $entity->title,
						'description' => $entity->content,
						'id' => $entity->id,
						'url' => URL::site(Ushahidi_Api::url($entity->getResource(), $entity->id), Request::current()),
						// @todo add mark- attributes based on tag symbol+color
						//'marker-size' => '',
						//'marker-symbol' => '',
						//'marker-color' => '',
						//'resource' => $entity
					]
				];
			}
		}

		return $output;
	}

	// CollectionFormatter
	public function getPaging(SearchData $input, $total_count)
	{
		if ($input->bbox)
		{
			if (is_array($input->bbox))
			{
				$bbox = $input->bbox;
			}
			else
			{
				$bbox = explode(',', $input->bbox);
			}

			return [
				'bbox' => $bbox
			];
		}

		return [];
	}

}
