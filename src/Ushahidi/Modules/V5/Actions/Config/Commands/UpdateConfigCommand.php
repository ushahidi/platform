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
    private $group;

    /**
     * @var array
     */
    private $update_configs;

    private $insert_configs;
    private $delete_configs;

    public function __construct(string $group, array $update_configs, array $insert_configs, array $delete_configs)
    {
        $this->group = $group;
        $this->update_configs = $update_configs;
        $this->insert_configs = $insert_configs;
        $this->delete_configs = $delete_configs;
    }

    public static function fromRequest(string $group, Request $request, array $current_configs): self
    {

        $update_configs = [];
        $insert_configs = [];
        $delete_configs = [];
        $new_configs = $request->input();
        foreach ($new_configs as $key => $value) {
            if ($key == 'id') {
                // ignor it
                continue;
            }
            if (key_exists($key, $current_configs)) {
                $update_configs[$key] = $value;
            } else {
                $insert_configs[$key] = $value;
            }
        }
        foreach ($current_configs as $key => $value) {
            if (!key_exists($key, $new_configs)) {
                $delete_configs[$key] = $value;
            }
        }


        return new self($group, $update_configs, $insert_configs, $delete_configs);
    }

    public function getGroup(): String
    {
        return $this->group;
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

    protected function verifyGroup($group)
    {
        if (!in_array($group, Config::AVIALABLE_CONFIG_GROUPS)) {
            throw new NotFoundException("Requested group does not exist: " . $group);
        }
    }
}
