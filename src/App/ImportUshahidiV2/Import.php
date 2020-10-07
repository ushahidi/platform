<?php

namespace Ushahidi\App\ImportUshahidiV2;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{

    protected $dateFormat = 'U';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETE = 'complete';

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'type' => 'ushahidiv2',
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
        'metadata' => 'json',
    ];

    protected $fillable = [
        'status',
        'type',
        'metadata',
    ];

    public function mappings()
    {
        return $this->hasMany(ImportMapping::class);
    }

    public function markComplete()
    {
        $this->status = self::STATUS_COMPLETE;

        return $this;
    }

    /**
     * Gets the value for a provided v2 setting, from the metadata
     */
    public function getV2Setting(string $key)
    {
        if ($this->metadata === null) {
            return null;
        }
        if (!array_key_exists('v2_settings', $this->metadata)) {
            return null;
        }
        if (!array_key_exists($key, $this->metadata['v2_settings'])) {
            return null;
        }
        return $this->metadata['v2_settings'][$key];
    }

    /**
     * Gets the value for a provided import parameter, from the metadata
     */
    public function getParameter(string $key, string ...$keys)
    {
        if ($this->metadata === null) {
            return null;
        }
        if (!array_key_exists('parameters', $this->metadata)) {
            return null;
        }

        if (!array_key_exists($key, $this->metadata['parameters'])) {
            return null;
        }
        
        $ret = $this->metadata['parameters'][$key];

        foreach ($keys as $k) {
            if (gettype($ret) !== "array" || !array_key_exists($k, $ret)) {
                return null;
            }
            $ret = $ret[$k];
        }
        return $ret;
    }

    public function getImportTimezone()
    {
        // 1. fixed (forced) timezone (from parameters file)
        // 2. v2 site configured timezone
        // 3. default timezone (from parameters file)
        if ($this->getParameter('timezones', 'force')) {
            return $this->getParameter('timezones', 'force');
        }
        if ($this->getV2Setting('site_timezone')) {
            return $this->getV2Setting('site_timezone');
        }
        return $this->getParameter('timezones', 'default');
    }
}
