<?php

/**
 * Ushahidi Export Job
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2018 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class ExportJob extends StaticEntity
{
    const STATUS_PENDING            = 'PENDING';
    const STATUS_QUEUED             = 'QUEUED';
    const STATUS_SUCCESS            = 'SUCCESS';
    const STATUS_FAILED             = 'FAILED';
    const STATUS_EXPORTED_TO_CDN    = 'EXPORTED_TO_CDN';
    const STATUS_PENDING_HDX        = 'PENDING_HDX';

     const DEFAULT_INCLUDE_HXL = 0;
     const DEFAULT_SEND_TO_BROWSER = 0;
     const DEFAULT_SEND_TO_HDX = 0;


    protected $id;
    protected $entity_type;
    protected $user_id;
    protected $fields;
    protected $filters;
    protected $status;
    protected $url;
    protected $header_row;
    protected $created;
    protected $updated;
    protected $url_expiration;
    protected $include_hxl;
    protected $send_to_browser;
    protected $send_to_hdx;
    protected $hxl_heading_row;
    protected $hxl_meta_data_id;
    protected $total_batches;
    protected $total_rows;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'                => 'int',
            'entity_type'       => 'string',
            'user_id'           => 'int',
            'status'            => 'string',
            'url'               => 'string',
            'fields'            => '*json',
            'filters'           => '*json',
            'header_row'        => '*json',
            'created'           => 'int',
            'updated'           => 'int',
            'url_expiration'    => 'int',
            'include_hxl'       => 'bool',
            'send_to_browser'   => 'bool',
            'send_to_hdx'       => 'bool',
            'hxl_heading_row'   => '*json',
            'hxl_meta_data_id'  => 'int',
            'total_batches'     => 'int',
            'total_rows'        => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'export_job';
    }

    // StatefulData
    protected function getDerived()
    {
        // Foreign key alias
        return [
            'hxl_meta_data_id'  => ['hxl_meta_data', 'hxl_meta_data.id'],
            'user_id'        => ['users', 'users.id']
        ];
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['user_id']);
    }

    public function isCombineBatchesDone()
    {
        return in_array(
            $this->status,
            [self::STATUS_SUCCESS, self::STATUS_EXPORTED_TO_CDN, self::STATUS_PENDING_HDX]
        );
    }
}
