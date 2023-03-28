<?php

/**
 * Ushahidi Post Geometry Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Repositories\Post;

use Ohanzee\DB;
use Ohanzee\Database;
use Symm\Gisconverter\Decoders\WKT;
use Symm\Gisconverter\Geometry\Point;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Symm\Gisconverter\Exceptions\InvalidText;
use Ushahidi\Core\Ohanzee\Entities\PostValue;

class PointRepository extends ValueRepository
{
    protected $decoder;

    public function __construct(OhanzeeResolver $resolver, WKT $decoder)
    {
        parent::__construct($resolver);
        $this->decoder = $decoder;
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_point';
    }

    protected $hideLocation = false;

    public function hideLocation($hide = true)
    {
        $this->hideLocation = $hide;
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        $map_config = service('map.config');
        try {
            $geometry = $this->decoder->geomFromText($data['value']);
            if ($geometry instanceof Point) {
                $data['value'] = ['lon' => $geometry->lon, 'lat' => $geometry->lat];
                if ($this->hideLocation) {
                    // Round to nearest 0.01 or roughly 500m
                    $data['value']['lat'] = round($data['value']['lat'], $map_config['location_precision']);
                    $data['value']['lon'] = round($data['value']['lon'], $map_config['location_precision']);
                }
            }
        } catch (InvalidText $e) {
            $data['value'] = ['lon' => null, 'lat' => null];
        }

        return new PostValue($data);
    }

    // Override selectQuery to fetch 'value' from db as text
    protected function selectQuery(array $where = [])
    {
        $query = parent::selectQuery($where);

        // Get geometry value as text
        $query->select(
            $this->getTable().'.*',
            // Fetch ST_AsText(value) aliased to value
                [DB::expr('ST_AsText(value)'), 'value']
        );
        return $query;
    }

    // Override prepareValue to save 'value' using GeomFromText
    protected function prepareValue($value)
    {
        if (is_array($value)) {
            $value = array_map('floatval', $value);
            $value = DB::expr("ST_GeomFromText('POINT(lon lat)')")->parameters($value);
        } else {
            $value = null;
        }

        return $value;
    }
}
