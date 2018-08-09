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
use Ohanzee\DB;
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

    public function getHxlWithFormAttributes($job)
    {
        // select form_attribute_hxl_attribute_tag.*, hxl_tags.tag_name, hxl_attributes.attribute
        // FROM form_attribute_hxl_attribute_tag
        // INNER JOIN hxl_tags ON form_attribute_hxl_attribute_tag.hxl_tag_id = hxl_tags.id
        // INNER JOIN hxl_attributes ON form_attribute_hxl_attribute_tag.hxl_attribute_id = hxl_attributes.id ;

        $result = DB::select(
            'form_attribute_hxl_attribute_tag.*',
            'form_attributes.key',
            'form_attributes.type',
            'form_attributes.input',
            'hxl_tags.tag_name',
            'hxl_attributes.attribute'
        )
            ->from($this->getTable())
            ->join('hxl_tags')->on('form_attribute_hxl_attribute_tag.hxl_tag_id', '=', 'hxl_tags.id')
            ->join('hxl_attributes')
            ->on('form_attribute_hxl_attribute_tag.hxl_attribute_id', '=', 'hxl_attributes.id')
            ->join('form_attributes')
            ->on('form_attribute_hxl_attribute_tag.form_attribute_id', '=', 'form_attributes.id')
            ->where('form_attribute_hxl_attribute_tag.export_job_id', '=', $job->id)
            ->execute($this->db)
            ->as_array();
        return $result;
    }
    public function getHxlTags($job)
    {
        $result = DB::select(
            'hxl_tags.tag_name'
        )
            ->from($this->getTable())
            ->join('hxl_tags')->on('form_attribute_hxl_attribute_tag.hxl_tag_id', '=', 'hxl_tags.id')
            ->join('hxl_attributes')
            ->on('form_attribute_hxl_attribute_tag.hxl_attribute_id', '=', 'hxl_attributes.id')
            ->join('form_attributes')
            ->on('form_attribute_hxl_attribute_tag.form_attribute_id', '=', 'form_attributes.id')
            ->where('form_attribute_hxl_attribute_tag.export_job_id', '=', $job->id)
            ->execute($this->db)
            ->as_array();
        return $result;
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
