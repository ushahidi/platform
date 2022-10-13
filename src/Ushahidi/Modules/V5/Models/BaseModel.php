<?php

namespace Ushahidi\Modules\V5\Models;

use Illuminate\Database\Eloquent\Model;
use Ushahidi\Modules\V5\Models\Concerns\HasSlug;
use Ushahidi\Modules\V5\Models\Concerns\HasValidator;
use Ushahidi\Modules\V5\Traits\HasOnlyParameters;

/**
 * Class ResourceModel
 * Base class for models that are exposed as HTTP resources
 * @package Ushahidi\Modules\V5\Models
 */
class BaseModel extends Model
{
    use HasSlug;
    Use HasValidator;
    use HasOnlyParameters;

    /**
     * @param $time timestamp
     */
    protected static function makeDate($time)
    {
        if (is_numeric($time) && !!$time) {
            $d = new \DateTime();
            $d->setTimestamp($time);
            return $d->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        }
        return $time;
    }
}
