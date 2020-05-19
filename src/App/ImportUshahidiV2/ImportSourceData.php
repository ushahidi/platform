<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Database\Eloquent\Model;

class ImportSourceData extends Model
{
    public $timestamps = false;

    protected $casts = [
        'import_id' => 'integer',
        'source_table' => 'string',
        'row_id' => 'string',
        'data' => 'object'
    ];

    protected $fillable = [
        'import_id',
        'source_table',
        'row_id',
        'data'
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
