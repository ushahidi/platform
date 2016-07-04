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

class Ushahidi_Formatter_Post_GeoJSON implements Formatter
{
	use GeoJSONFormatter;

	// Formatter
	public function __invoke($entity)
	{
		$features = array();
		foreach($entity->values as $attribute => $values)
		{
			foreach($values as $value)
			{
				if ($geometry = $this->valueToGeometry($value))
				{
					$color = ltrim($entity->color, '#');
					$color = $color ? '#' . $color : null;

					$features[] = [
						'type' => 'Feature',
						'geometry' => $geometry,
						'properties' => [
							'title' => $entity->title,
							'description' => $entity->content,
							'marker-color' => $color,
							'id' => $entity->id,
							'attribute_key' => $attribute
							// @todo add mark- attributes based on tag symbol+color
							//'marker-size' => '',
							//'marker-symbol' => '',
							//'resource' => $post
						]
					];
				}
			}
		}

		return [
			'type' => 'FeatureCollection',
			'features' => $features
			// @todo include bbox
		];
	}

}
