<?php
namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Contracts\Entity;

interface ExportBatch extends Entity
{
    const STATUS_PENDING     = 'pending';

    const STATUS_COMPLETED   = 'completed';

    const STATUS_FAILED      = 'failed';

}
