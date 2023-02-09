<?php

/**
 * Ushahidi HXLTag Repository, using Kohana::$config
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository\HXL;

use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Core\Entity\HXL\HXLTag;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Modules\V3\Repository\OhanzeeRepository;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Contracts\Repository\Entity\HXLTagRepository as HXLTagRepositoryContract;

class HXLTagRepository extends OhanzeeRepository implements
    HXLTagRepositoryContract,
    SearchRepository,
    ReadRepository
{
    private $tags_attributes;

    public function __construct(OhanzeeResolver $resolver)
    {
        parent::__construct($resolver);
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'hxl_tags';
    }

    public function getSearchFields()
    {
        return ['tag_name'];
    }

    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    /**
     * @param array|null $data
     * @return \Ushahidi\Modules\V3\Repository\Ushahidi\Core\Entity|HXLTag|\Ushahidi\Contracts\Repository\Usecase\Entity
     */
    public function getEntity(array $data = null)
    {
        if ($data) {
            $data['form_attribute_types'] = $this->getFormAttributeTypes($data['id']);
            $data['hxl_attributes'] = $this->getAllHXLAttributes($data['id']);
        }
        return new HXLTag($data);
    }

    /**
     * @param $hxl_tag_id
     */
    protected function getAllHXLAttributes($tag_id)
    {
        if (!$this->tags_attributes) {
            $this->tags_attributes =  DB::select(
                ['hxl_tags.id', 'hxl_tag_id'],
                'hxl_attributes.id',
                'hxl_attributes.attribute',
                'hxl_attributes.description'
            )
                ->from('hxl_tags')
                ->join('hxl_tag_attributes')
                ->on('hxl_tags.id', '=', 'hxl_tag_attributes.tag_id')
                ->join('hxl_attributes')
                ->on('hxl_tag_attributes.attribute_id', '=', 'hxl_attributes.id')->execute($this->db())->as_array();
        }

        return array_map(function ($tag) {
            $tag['hxl_tag_id'] = intval($tag['hxl_tag_id']);
            $tag['id'] = intval($tag['id']);
            return $tag;
        }, array_filter($this->tags_attributes, function ($tag_attributes) use ($tag_id) {
            return $tag_attributes['hxl_tag_id'] === $tag_id;
        }));
    }

    /**
     * Get all attribute types that can be matched to this hxl tag
     * @param $hxl_tag_id
     * @return mixed
     */
    protected function getFormAttributeTypes($hxl_tag_id)
    {
        return DB::select('form_attribute_type')
            ->from('hxl_attribute_type_tag')
            ->where('hxl_tag_id', '=', $hxl_tag_id)
            ->execute($this->db())->as_array();
    }
}
