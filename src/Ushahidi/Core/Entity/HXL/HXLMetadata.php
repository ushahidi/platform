<?php

/**
 * Ushahidi HXLMetadata Entity
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\StaticEntity;

class HXLMetadata extends StaticEntity
{
    protected $id;
    protected $private;
    protected $dataset_title;
    protected $license_id;
    protected $user_id;
    protected $organisation_id;
    protected $organisation_name;
    protected $source;
    protected $created;
    protected $updated;

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id'                => 'int',
            'private'           => 'bool',
            'dataset_title'     => 'string',
            'license_id'        => 'int',
            'user_id'           => 'int',
            'organisation_id'   => 'string',
            'organisation_name' => 'string',
            'source'            => 'string',
            'created'           => 'int',
            'updated'           => 'int',
        ];
    }

    // StatefulData
    protected function getDerived()
    {
        // Foreign key alias
        return [
            'license_id'     => ['hxl_license', 'hxl_license.id'],
            'export_job_id'  => ['export_job', 'export_job.id'],
            'user_id'        => ['users', 'users.id']
        ];
    }


    // Entity
    public function getResource()
    {
        return 'hxl_meta_data';
    }
}
