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

        return collect($group_configs);
    }

    protected function verifyGroup($group)
    {
        if (!in_array($group, Config::AVIALABLE_CONFIG_GROUPS)) {
            throw new NotFoundException("Requested group does not exist: " . $group);
        }
    }

    protected function getDefaults($group)
    {
        // Just in case we find some other path here
        // We absolutely have to validate the group
        // since we're now using it as a file name
        $this->verifyGroup($group);

        // @todo add them to config!
        $file = __DIR__."/../../../.." . '/V3/Repository/Config/' . $group . '.php';
        if (file_exists($file)) {
            return require $file;
        }
        return [];
    }
}
