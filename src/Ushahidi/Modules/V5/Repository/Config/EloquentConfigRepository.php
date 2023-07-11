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

    public function createByKey(string $group_name, string $key, $value)
    {
        $config = Config::create([
          "group_name" => $group_name,
          "config_key" => $key,
          "config_value" => $value
     
        ]);
    }
    public function updateOrInsertByKey(string $group_name, string $key, $value)
    {

        $config = Config::where(["group_name"=>$group_name,"config_key"=>$key])->first();
        if ($config) {
            $config->config_value = $value;
            $config->save();
        } else {
            $this->createByKey($group_name, $key, $value);
        }
    }
    public function deleteByKey(string $group_name, string $key)
    {
         Config::where(["group_name"=>$group_name,"config_key"=>$key])->delete();
    }
}
