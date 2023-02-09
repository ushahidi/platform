<?php

namespace Ushahidi\Modules\V5\Exceptions\Http;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Exceptions\V5Exception;

class HttpResponsableException extends V5Exception
{
    public static function providedValueOfWrongType(string $instance): self
    {
        return new self(
            sprintf(
                'Provided instance of class `%s` does not extend neither %s or %s',
                $instance,
                Model::class,
                Collection::class
            )
        );
    }
}
