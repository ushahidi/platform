<?php

namespace Ushahidi\Modules\V5\Models;

/**
 * @property int $id
 * @property string $country_name
 * @property string $dial_code
 * @property string $country_code
 */
class CountryCode extends BaseModel
{
    protected $table = 'country_codes';
    public $timestamps = false;
}
