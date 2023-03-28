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

class GeometryRepository extends ValueRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_geometry';
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

    protected function prepareValue($value)
    {
        return DB::expr('ST_GeomFromText(:text)')->param(':text', $value);
    }
}
