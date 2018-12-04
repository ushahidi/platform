<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{

    protected $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    const STATUS_PENDING = 'pending';

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'type' => 'ushahidiv2',
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    protected $fillable = [
        'status',
        'type'
    ];

    public function mappings()
    {
        return $this->hasMany(ImportMapping::class);
    }
}
