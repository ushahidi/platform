<?php

/**
 * Ushahidi Post Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

//use Ushahidi\Core\Entity\PostValue;
//use Ushahidi\Core\Entity\PostValueRepository as PostValueRepositoryContract;

class MediaRepository extends ValueRepository
{

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        /**
         * This value is added here so that we can manipulate it in the CSV getpostvalues and use either id or filename
         * depending on the repository used
         */
        $data['value'] = ['o_filename' => $data['o_filename'], 'id' => $data['id']];
        return new \Ushahidi\Core\Entity\PostValueMedia($data);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_media';
    }

    // Override selectQuery to fetch attribute 'key' too
    protected function selectQuery(array $where = [])
    {
        $query = parent::selectQuery($where);

        // Select 'key' too
        $query->select(
            'media.o_filename',
            'media.id'
        )
        ->join('media')->on('value', '=', 'media.id');

        return $query;
    }
}
