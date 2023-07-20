<?php
namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;

interface ExportJob extends Entity
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_QUEUED = 'QUEUED';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_EXPORTED_TO_CDN = 'EXPORTED_TO_CDN';
    const STATUS_PENDING_HDX = 'PENDING_HDX';

    const DEFAULT_INCLUDE_HXL = 0;
    const DEFAULT_SEND_TO_BROWSER = 0;
    const DEFAULT_SEND_TO_HDX = 0;
}
