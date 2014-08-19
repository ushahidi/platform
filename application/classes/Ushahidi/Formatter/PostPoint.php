<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post Point Values
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
use Symm\Gisconverter\Decoders\WKT;
use Symm\Gisconverter\Geometry\Point;
use Symm\Gisconverter\Exceptions\InvalidText;

class Ushahidi_Formatter_PostPoint extends Ushahidi_Formatter_API
{
	protected $decoder;

	public function __construct(WKT $decoder)
	{
		$this->decoder = $decoder;
	}

	public function __invoke($entity)
	{
		return [
			'id'    => $entity->id,
			'value' => $this->format_value($entity->value)
		];
	}

	protected function format_value($value)
	{
		try
		{
			$geometry = $this->decoder->geomFromText($value);
			if ($geometry instanceof Point)
				return array('lon' => $geometry->lon, 'lat' => $geometry->lat);
		}
		catch (InvalidText $itex)
		{
			// noop - continue to return raw value
		}

		return $value;
	}
}
