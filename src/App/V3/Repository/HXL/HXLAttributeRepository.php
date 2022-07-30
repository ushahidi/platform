<?php

/**
 * Ushahidi HXLTag Repository, using Kohana::$config
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Repository\HXL;

use Ohanzee\Database;
use Ushahidi\Core\Tools\SearchData;
use Ushahidi\Core\Entity\HXL\HXLAttribute;
use Ushahidi\App\V3\Repository\OhanzeeRepository;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Contracts\Repository\Entity\HXLAttributeRepository as HXLAttributeRepositoryContract;

class HXLAttributeRepository extends OhanzeeRepository implements
    HXLAttributeRepositoryContract,
    SearchRepository,
    ReadRepository
{
    private $tags_attributes;

    public function __construct(\Ushahidi\App\Multisite\OhanzeeResolver $resolver)
    {
        parent::__construct($resolver);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'hxl_attributes';
    }

    public function getSearchFields()
    {
        return ['attribute'];
    }

    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    /**
     * @param array|null $data
     * @return \Ushahidi\Contracts\Entity
     */
    public function getEntity(array $data = null)
    {
        return new HXLAttribute($data);
    }
}
