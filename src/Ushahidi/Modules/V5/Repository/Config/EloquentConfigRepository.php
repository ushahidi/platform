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

    public function create(string $group_name, string $key, $value)
    {
    }
    public function updateByKey(string $group_name, string $key, $value)
    {
        $config = Config::where(["group_name"=>$group_name,"config_key"=>$key])->first();
        $config->config_value = $value;
    }
    public function deleteByKey(string $group_name, string $key)
    {
         Config::where(["group_name"=>$group_name,"config_key"=>$key])->delete();
    }
}
