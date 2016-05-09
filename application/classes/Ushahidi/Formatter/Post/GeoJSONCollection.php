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
				$set_ids = $entity->sets;
				$query = DB::select('sets.name')
							->from('sets')
							->where('id', 'IN', $setIds);
				$set_names = $query->execute();
				$set_name = [];
				foreach ($set_names as $tmp_set_name) {
					$set_name[] = $tmp_set_name['name'];
				}

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
						'url' => URL::site(Ushahidi_Rest::url($entity->getResource(), $entity->id), Request::current()),
						'set_name' => $set_name,
						// @todo add mark- attributes based on tag symbol+color
						//'marker-size' => '',
						//'marker-symbol' => '',
						//'marker-color' => '',
						//'resource' => $entity
					]
				];
			}
		}

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
