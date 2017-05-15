<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Tool\Formatter\GeoJSONFormatter;
use Ushahidi\Core\SearchData;
use Ushahidi\App\Http\Controllers\RESTController;

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
		$unmapped = 0;

		foreach ($entities as $entity)
		{
			$geometries = [];
			foreach($entity->values as $attribute => $values)
			{
				foreach ($values as $value)
				{
					if ($geometry = $this->valueToGeometry($value))
					{
						$geometries[] = $geometry;
					}
				}
			}

			if (! empty($geometries))
			{
				$color = ltrim($entity->color, '#');
				$color = $color ? '#' . $color : null;

				$output['features'][] = [
					'type' => 'Feature',
					'geometry' => [
						'type' => 'GeometryCollection',
						'geometries' => $geometries
					],
					'properties' => [
						'title' => $entity->title,
						'description' => $entity->content,
						'marker-color' => $color,
						'id' => $entity->id,
						'url' => url(RESTController::url($entity->getResource(), $entity->id)),
						// @todo add mark- attributes based on tag symbol+color
						//'marker-size' => '',
						//'marker-symbol' => '',
						//'resource' => $entity
					]
				];
			}
			if(empty($geometries))
			{
				$unmapped++;
			}
		}
		$output['unmapped'] = $unmapped;

		if ($this->search->bbox)
		{
			if (is_array($this->search->bbox))
			{
				$bbox = $this->search->bbox;
			}
			else
			{
				$bbox = explode(',', $this->search->bbox);
			}

			$output['bbox'] = $bbox;
		}
		return $output;
	}

	/**
	 * Store paging parameters.
	 *
	 * @param  SearchData $search
	 * @param  Integer    $total
	 * @return $this
	 */
	public function setSearch(SearchData $search, $total = null)
	{
		$this->search = $search;
		$this->total  = $total;
		return $this;
	}

}
