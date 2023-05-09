<?php

namespace Ushahidi\Modules\V5\Actions\Config\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ushahidi\Modules\V5\Models\Config;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Modules\V5\Repository\Config\ConfigRepository;
use Ushahidi\Modules\V5\Actions\Config\Commands\UpdateConfigCommand;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\Config as ConfigEntity;

class UpdateConfigCommandHandler extends AbstractCommandHandler
{
       // Use Event trait to trigger events
       use Event;
    private $config_repository;

    public function __construct(ConfigRepository $config_repository)
    {
        $this->config_repository = $config_repository;
    }

    protected function isSupported(Command $command): void
    {
        if (!$command instanceof UpdatePUpdateConfigCommandostCommand) {
            throw new \Exception('Provided $command is not instance of UpdateConfigCommand');
        }
    }

    public function __invoke(Action $action)
    {
        /**
         * @var UpdateConfigCommand $action
         */
        $this->isSupported($action);

     
         $this->updateConfig($action);
    }


    private function updateConfig(UpdateConfigCommand $action)
    {
        $intercom_data = [];
        $group = $action->getGroup();

 //       $this->verifyGroup($group);

        if ($group == 'deployment_id') {
            return; /* noop */
        }

        // Intercom count datasources
        if ($group === 'data-provider') {
            $intercom_data['num_data_sources'] = 0;
            foreach ($entity->providers as $key => $value) {
                $value ? $intercom_data['num_data_sources']++ : null;
            }
        }

        $immutable = $entity->getImmutable();
        foreach ($entity->getChanged() as $key => $val) {
            // Emit Intercom Update events
            if ($key === 'description') {
                $intercom_data['has_description'] = true;
            }

            if ($key === 'image_header') {
                $intercom_data['has_logo'] = true;
            }

            // New User - set their deployment created date
            if ($key === 'first_login') {
                $intercom_data['deployment_created_date'] = date("Y-m-d H:i:s");
            }

            if (! in_array($key, $immutable)) {
                $this->insertOrUpdate($group, $key, $val);
            }
        }

        if ($intercom_data) {
            $this->emit($this->event, $intercom_data);
        }
    }


    public function update(Entity $entity)
    {
        $intercom_data = [];
        $group = $entity->getId();

        $this->verifyGroup($group);

        if ($group == 'deployment_id') {
            return; /* noop */
        }

        // Intercom count datasources
        if ($group === 'data-provider') {
            $intercom_data['num_data_sources'] = 0;
            foreach ($entity->providers as $key => $value) {
                $value ? $intercom_data['num_data_sources']++ : null;
            }
        }

        $immutable = $entity->getImmutable();
        foreach ($entity->getChanged() as $key => $val) {
            // Emit Intercom Update events
            if ($key === 'description') {
                $intercom_data['has_description'] = true;
            }

            if ($key === 'image_header') {
                $intercom_data['has_logo'] = true;
            }

            // New User - set their deployment created date
            if ($key === 'first_login') {
                $intercom_data['deployment_created_date'] = date("Y-m-d H:i:s");
            }

            if (! in_array($key, $immutable)) {
                $this->insertOrUpdate($group, $key, $val);
            }
        }

        if ($intercom_data) {
            $this->emit($this->event, $intercom_data);
        }
    }

    private function insertOrUpdate($group, $key, $value)
    {
        $value = json_encode($value);

        DB::query(Database::INSERT, "
			INSERT INTO `config`
			(`group_name`, `config_key`, `config_value`) VALUES (:group, :key, :value)
			ON DUPLICATE KEY UPDATE `config_value` = :value;
		")
        ->parameters([
            ':group' => $group,
            ':key' => $key,
            ':value' => $value
        ])
        ->execute($this->db());
    }

    protected function verifyGroup($group)
    {
        if (!in_array($group, Config::AVIALABLE_CONFIG_GROUPS)) {
            throw new NotFoundException("Requested group does not exist: " . $group);
        }
    }
}
