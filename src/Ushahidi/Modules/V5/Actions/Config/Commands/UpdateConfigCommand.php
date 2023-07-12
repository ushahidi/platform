<?php

namespace Ushahidi\Modules\V5\Actions\Config\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Config;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Http\Request;

class UpdateConfigCommand implements Command
{

    /**
     * @var string
     */
    private $group_name;

    /**
     * @var array
     */
    private $update_configs;

    private $insert_configs;
    private $delete_configs;

    public function __construct(string $group_name, array $update_configs, array $insert_configs, array $delete_configs)
    {
        $this->group_name = $group_name;
        $this->update_configs = $update_configs;
        $this->insert_configs = $insert_configs;
        $this->delete_configs = $delete_configs;
    }

    public static function fromRequest(
        string $group_name,
        Request $request,
        array $current_configs,
        ?string $config_key = null
    ): self {

        $update_configs = [];
        $insert_configs = [];
        $delete_configs = [];
        $new_configs = $request->input();
        // case it is key
        if ($config_key) {
            $value = $new_configs;
            if (self::isDataProvider($group_name)) {
                foreach ($current_configs as $old_key => $old_value) {
                    if ($old_key === 'id' || $old_key === "allowed_privileges") {
                        continue;
                    }
                    $providers[$old_key] = $old_value['enabled'];
                }
                $providers[$config_key] = $value["enabled"];
                $value = $value['params'];
            } elseif (count($new_configs) === 1 && array_keys($new_configs) === [0]) {
                $value = $new_configs[0];
            }
            if (key_exists($config_key, $current_configs)) {
                $update_configs[$config_key] = $value;
            } else {
                $insert_configs[$config_key] = $value;
            }
        } else {
                $providers = [] ;//=   $current_configs['providers']
                $authenticable = [] ;
            foreach ($new_configs as $key => $value) {
                if ($key === 'id' || $key === "allowed_privileges") {
                    continue;
                }
                if (self::isDataProvider($group_name)) {
                    $providers[$key] = $value['enabled'];
                    $value = $value['params'];
                }
                    
                if (key_exists($key, $current_configs)) {
                    $update_configs[$key] = $value;
                } else {
                    $insert_configs[$key] = $value;
                }
            }
            foreach ($current_configs as $key => $value) {
                if ($key === 'id' || $key === "allowed_privileges") {
                    continue;
                }

                if (self::isDataProvider($group_name)) {
                    $value = $value['params'];
                }

                if (!key_exists($key, $new_configs)) {
                    $delete_configs[$key] = $value;
                }
            }
        }
        if (self::isDataProvider($group_name)) {
            $update_configs["providers"] = $providers;
        }
        return new self($group_name, $update_configs, $insert_configs, $delete_configs);
    }

    protected static function isDataProvider($group_name)
    {
        return $group_name === "data-provider";
    }
    
    public function getGroupName(): String
    {
        return $this->group_name;
    }

    /**
     * @return array
     */
    public function getUpdateConfigs(): array
    {
        return $this->update_configs;
    }

    public function getInsertConfigs(): array
    {
        return $this->insert_configs;
    }

    public function getDeleteConfigs(): array
    {
        return $this->delete_configs;
    }
}
