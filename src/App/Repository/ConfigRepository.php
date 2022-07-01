<?php
/**
 * Ushahidi Config Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Contracts\Entity;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Entity\Config as ConfigEntity;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\DeleteRepository;
use Ushahidi\Contracts\Repository\UpdateRepository;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository as ConfigRepositoryContract;

class ConfigRepository implements
    ReadRepository,
    UpdateRepository,
    DeleteRepository,
    ConfigRepositoryContract
{

    // Use Event trait to trigger events
    use Event;

    protected $resolver;

    public function __construct(OhanzeeResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get current connection
     *
     * @return Ohanzee\Database;
     */
    protected function db()
    {
        return $this->resolver->connection();
    }

    // ReadRepository
    public function getEntity(array $data = null)
    {
        return new ConfigEntity($data);
    }

    protected function getDefaults($group)
    {
        // Just in case we find some other path here
        // We absolutely have to validate the group
        // since we're now using it as a file name
        $this->verifyGroup($group);

        // @todo replace with separate entities
        $file = __DIR__ . '/Config/' . $group . '.php';
        if (file_exists($file)) {
            return require $file;
        }

        return [];
    }

    // ReadRepository
    // ConfigRepository
    public function get($group)
    {
        $this->verifyGroup($group);

        if ($group == 'deployment_id') {
            return $this->getDeploymentIdGroup();
        }

        $config = [];
        try {
            $query = DB::select('config.*')
                ->from('config')
                ->where('group_name', '=', $group)
                ->execute($this->db());

            if (count($query)) {
                $config = $query->as_array('config_key', 'config_value');
                $config = array_map(function ($config_value) {
                    return json_decode($config_value, true);
                }, $config);
            }
        } catch (\Ohanzee\Database\Exception $e) {
            // If error was NOT because table doesn't exist
            if (!preg_match("/Table '.*' doesn't exist/", $e->getMessage())) {
                // Throw the error again
                throw $e;
            }

            // Otherwise, Config table doesn't exist: continue to default values
        }

        // Merge defaults
        $defaults = $this->getDefaults($group);

        $config = array_replace_recursive($defaults, $config);

        return $this->getEntity(['id' => $group] + $config);
    }

    protected function getDeploymentIdGroup()
    {
        // The Deployment Id group is special in that it's not persisted in the deployment's database.
        $config = [];

        // Multisite IDs
        if (app('multisite')->enabled()) {
            $multi = app('multisite');
            $config['multisite'] = [
                'enabled' => true,
                'site_id' => $multi->getSiteId(),
                'site_fqdn' => $multi->getSite()->getClientUri(),
            ];
        } else {
            $config['multisite'] = [];
        }

        $analytics_prefix = env('USH_ANALYTICS_PREFIX', null);
        $analytics_id =
            $config['multisite']['site_id'] ??
            env('USH_ANALYTICS_ID', null);
        if ($analytics_prefix && $analytics_id) {
            $config['analytics'] = [
                'prefix' => $analytics_prefix,
                'id' => $analytics_id,
            ];
        } else {
            $config['analytics'] = [];
        }

        return $this->getEntity(['id' => 'deployment_id'] + $config);
    }

    // UpdateRepository
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

    // DeleteRepository
    public function delete(Entity $entity)
    {
        $group = $entity->getId();

        DB::delete('config')->where('group_name', '=', $group)->execute($this->db());
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

    // ConfigRepository
    public function groups()
    {
        return [
            'features',
            'site',
            'deployment_id',
            'test',
            'data-provider',
            'map',
            'twitter',
            'gmail'
        ];
    }

    /**
     * @param  string $group
     * @throws InvalidArgumentException when group is invalid
     * @return void
     */
    protected function verifyGroup($group)
    {
        if ($group && !in_array($group, $this->groups())) {
            throw new NotFoundException('Requested group does not exist: ' . $group);
        }
    }

    /**
     * @param  array $groups
     * @throws InvalidArgumentException when any group is invalid
     * @return void
     */
    protected function verifyGroups(array $groups)
    {
        $invalid = array_diff(array_values($groups), $this->groups());
        if ($invalid) {
            throw new NotFoundException(
                'Requested groups do not exist: ' . implode(', ', $invalid)
            );
        }
    }

    // ConfigRepository
    public function all(array $groups = null)
    {
        if ($groups) {
            $this->verifyGroups($groups);
        } else {
            $groups = $this->groups();
        }

        $result = [];
        foreach ($groups as $group) {
            $result[] = $this->get($group);
        }

        return $result;
    }
}
