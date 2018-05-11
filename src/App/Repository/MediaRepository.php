<?php

/**
 * Ushahidi Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ohanzee\Database;
use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Media;
use Ushahidi\Core\Entity\MediaRepository as MediaRepositoryContract;
use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Tool\UploadData;

class MediaRepository extends OhanzeeRepository implements
    MediaRepositoryContract
{
    private $upload;

    private $created_id;
    private $created_ts;

    private $deleted_media;

    public function __construct(Database $db, Uploader $upload)
    {
        parent::__construct($db);

        $this->upload = $upload;
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'media';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new Media($data);
    }

    // SearchRepository
    public function getSearchFields()
    {
        return ['user', 'orphans'];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        if ($search->user) {
            $this->search_query->where('user_id', '=', $search->user);
        }

        if ($search->orphans) {
            $this->search_query
                ->join('posts_media', 'left')
                    ->on('posts_media.media_id', '=', 'media.id')
                ->where('posts_media.post_id', 'is', null);
        }
    }
}
