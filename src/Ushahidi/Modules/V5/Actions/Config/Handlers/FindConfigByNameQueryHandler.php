<?php

namespace Ushahidi\Modules\V5\Actions\Config\Handlers;

use App\Bus\Action;
use App\Bus\Query\Query;
use App\Bus\Query\AbstractQueryHandler;
use Ushahidi\Modules\V5\Actions\Config\Queries\FindConfigByNameQuery;
use Ushahidi\Modules\V5\Repository\Config\ConfigRepository;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Core\Exception\NotFoundException;

class FindConfigByNameQueryHandler extends AbstractQueryHandler
{
    private $config_repository;

    public function __construct(ConfigRepository $config_repository)
    {
        $this->config_repository = $config_repository;
    }

    protected function isSupported(Query $query)
    {
        if (!$query instanceof FindConfigByNameQuery) {
            throw new \InvalidArgumentException('Provided action is not supported');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var FindConfigByNameQuery $action
         */
        $this->isSupported($action);

        $this->verifyGroup($action->getGroupName());
        $configs = $this->config_repository->findByGroupName($action->getGroupName());
        $group_configs = ['id' => $action->getGroupName()];
        foreach ($configs as $config) {
            $group_configs[$config->config_key] = $config->config_value;
        }


        // Merge defaults
        $defaults = $this->getDefaults($action->getGroupName());
        $group_configs = array_replace_recursive($defaults, $group_configs);


        // handle data provider
        if ($action->getGroupName() === "data-provider") {
            if ($action->getKey()) {
                $this->verifyDataProvider($group_configs, $action->getKey());
                return collect($this->getOneDataProvider($group_configs, $action->getKey()));
            }
            return collect($this->getDataProvider($group_configs));
        }
        if ($action->getKey()) {
            $key_config = [
                "group_name" => $action->getGroupName(),
                "key_name" => $action->getKey(),
                "key_value" => $group_configs[$action->getKey()]
            ];
            return collect($key_config);
        }
        return collect($group_configs);
    }

    protected function getDataProvider($raw_data_providers)
    {
        $data_providers = [];
        $data_providers['id'] = "data-provider";
        foreach ($raw_data_providers['providers'] as $provider_name => $provider_status) {
            $data_providers[$provider_name] = $this->getOneDataProvider($raw_data_providers, $provider_name);
        }
        return $data_providers;
    }
    protected function getOneDataProvider($raw_data_providers, $provider_name)
    {
        $data_provider["provider-name"] = $provider_name;
        $data_provider["enabled"] = $raw_data_providers['providers'][$provider_name];
        // $data_provider["authenticable"] =
        //     isset($raw_data_providers["authenticable-providers"][$provider_name])
        //     ? $raw_data_providers["authenticable-providers"][$provider_name]
        //     : false;
        $data_provider["params"] = isset($raw_data_providers[$provider_name])
            ? $raw_data_providers[$provider_name]
            : [];
        return $data_provider;
    }
    protected function verifyGroup($group)
    {
        if (!in_array($group, Config::AVIALABLE_CONFIG_GROUPS)) {
            throw new NotFoundException("Requested group does not exist: " . $group);
        }
    }

    protected function verifyKey($group_config, $key)
    {
        if (!in_array($key, array_keys($group_config))) {
            throw new NotFoundException("Requested config does not exist: " . $key);
        }
    }


    protected function verifyDataProvider($data_providers, $provider_name)
    {
        if (!in_array($provider_name, array_keys($data_providers['providers']))) {
            throw new NotFoundException("Requested config does not exist: " . $provider_name);
        }
    }


    protected function getDefaults($group)
    {
        // Just in case we find some other path here
        // We absolutely have to validate the group
        // since we're now using it as a file name
        $this->verifyGroup($group);

        // @todo add them to config!
        $file = __DIR__ . "/../../../.." . '/V3/Repository/Config/' . $group . '.php';
        if (file_exists($file)) {
            return require $file;
        }
        return [];
    }
}
