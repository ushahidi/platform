<?php

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

use Ushahidi\Core\Entity\PostValue;
use Ushahidi\Core\Entity\PostValueRepository as PostValueRepositoryContract;

class IntegerRepository extends ValueRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_int';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        $data['value'] = intval($data['value']);
        return new PostValue($data);
    }
}
