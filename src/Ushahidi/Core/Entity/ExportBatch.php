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

class ExportBatch extends StaticEntity
{
    const STATUS_PENDING     = 'pending';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_FAILED      = 'failed';

    protected $id;
    protected $export_job_id;
    protected $batch_number;
    protected $status;
    protected $filename;
    protected $url;
    protected $has_header;
    protected $rows;
    protected $created;
    protected $updated;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'                => 'int',
            'export_job_id'     => 'int',
            'batch_number'      => 'int',
            'status'            => 'string',
            'filename'          => 'string',
            'url'               => 'string',
            'has_header'        => 'int',
            'rows'              => 'int',
            'created'           => 'int',
            'updated'           => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'export_batch';
    }
}
