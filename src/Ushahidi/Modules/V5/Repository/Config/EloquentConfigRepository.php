<?php

namespace Ushahidi\Modules\V5\Repository\Config;

use Ushahidi\Modules\V5\Models\Config;

class EloquentConfigRepository implements ConfigRepository
{
     /**
     * This method will fetch a all configs of group
     * @param string $group_name
     * @return Config
     */
    public function findByGroupName(string $group_name)
    {
         return Config::select("config_key", "config_value")->where('group_name', $group_name)->get();
    }
}
