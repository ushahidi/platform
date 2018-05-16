<?php

/**
 * Ushahidi HXLFormAttributeHXLAttributeTagRepository Repository, using Kohana::$config
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\HXL;

use Ohanzee\Database;
use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTag;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTagRepository
    as HXLFormAttributeHXLAttributeTagRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\SearchRepository;
use Ushahidi\App\Repository\OhanzeeRepository;

class HXLFormAttributeHXLAttributeTagRepository extends OhanzeeRepository implements
    HXLFormAttributeHXLAttributeTagRepositoryContract,
    SearchRepository,
    ReadRepository
{
    public function __construct(Database $db)
    {
        parent::__construct($db);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'form_attribute_hxl_attribute_tag';
    }

    public function getSearchFields()
    {
        return [];
    }

    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    /**
     * @param array|null $data
     * @return \Ushahidi\App\Repository\Ushahidi\Core\Entity|HXLTag|\Ushahidi\Core\Usecase\Entity
     */
    public function getEntity(array $data = null)
    {
        return new HXLFormAttributeHXLAttributeTag($data);
    }
}
