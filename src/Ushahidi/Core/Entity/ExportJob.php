<?php
namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Contracts\Entity;

interface ExportJob extends Entity
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_QUEUED = 'QUEUED';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_EXPORTED_TO_CDN = 'EXPORTED_TO_CDN';
    const STATUS_PENDING_HDX = 'PENDING_HDX';
}
