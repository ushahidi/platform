<?php
/**
 * Ushahidi GeoJSON Formatter Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Formatter;

use Symm\Gisconverter\Decoders\WKT;
use Symm\Gisconverter\Exceptions\InvalidText;

trait GeoJSONFormatter
{
	protected $decoder;

	public function setDecoder(WKT $decoder)
	{
		$this->decoder = $decoder;
	}

	/**
	 * Create a GeoJSON geometry from form field value
	 *
	 * @param  array|string $value    Value
	 * @param  string       $type     Value Type (point or geometry)
	 * @return array
	 */
	protected function valueToGeometry($value)
	{
		// Assume array value's are always lat/lon arrays
		if (is_array($value)) {
			return [
				'type' => 'Point',
				'coordinates' => [$value['lon'], $value['lat']]
			];
		}

		// Otherwise assume value is WKT text
		try {
			$geometry = $this->decoder->geomFromText($value);
			return $geometry->toGeoArray();
		} catch (InvalidText $itex) {
			// Invalid value, just skip it
			return [];
		}
	}
}
