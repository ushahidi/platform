<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{

    public $timestamps = false;

    protected $attributes = [];

    protected $casts = [
        'import_id' => 'integer',
        'source_type' => 'string',
        'source_id' => 'integer',
        'dest_type' => 'string',
        'dest_id' => 'integer',
    ];

    protected $fillable = [
        'import_id',
        'source_type',
        'source_id',
        'dest_type',
        'dest_id',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
