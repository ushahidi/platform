<?php

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository\Post;

class VarcharRepository extends ValueRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_varchar';
    }
}
