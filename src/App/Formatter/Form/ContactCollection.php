<?php

/**
 * Ushahidi API Formatter for Form Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter\Form;

use Ushahidi\App\Formatter\API;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Ushahidi\Core\SearchData;

class ContactCollection extends API
{
    use FormatterAuthorizerMetadata;

    public function __invoke($entities = [])
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->asArray();
        }
        return $data;
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
