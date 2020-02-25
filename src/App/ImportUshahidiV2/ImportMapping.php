<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{

    public $timestamps = false;

    protected $attributes = [
        'established_by' => 'import-mapper'     # default value
    ];

    protected $casts = [
        'import_id' => 'integer',
        'source_type' => 'string',
        'source_id' => 'string',
        'dest_type' => 'string',
        'dest_id' => 'integer',
        'established_by' => 'string'
    ];

    protected $fillable = [
        'import_id',
        'source_type',
        'source_id',
        'dest_type',
        'dest_id',
        'established_by'
    ];

    private static $established_by_allowed_values = [ 'import-mapper', 'import-config', 'duplicate-detection' ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function setEstablishedByAttribute($value)
    {
        $value = strtolower($value);
        if (in_array($value, ImportMapping::$established_by_allowed_values)) {
            $this->attributes['established_by'] = $value;
        } else {
            throw new InvalidArgumentException('"established_by"');
        }
    }
}
