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

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Config as ConfigEntity;
use Ushahidi\Core\Entity\ConfigRepository as ConfigRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\UpdateRepository;
use Ushahidi\Core\Exception\NotFoundException;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;
use Ohanzee\DB;
use Ohanzee\Database;

class ConfigRepository implements
    ReadRepository,
    UpdateRepository,
    ConfigRepositoryContract
{

    // Use Event trait to trigger events
    use Event;

    public function __construct(Database $db)
    {
        $this->db = $db;
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

        $config = [];
        try {
            $query = DB::select('config.*')
                ->from('config')
                ->where('group_name', '=', $group)
                ->execute($this->db);

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

    // UpdateRepository
    public function update(Entity $entity)
    {
        $intercom_data = [];
        $group = $entity->getId();

        $this->verifyGroup($group);

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
        ->execute($this->db);
    }

    // ConfigRepository
    public function groups()
    {
        return [
            'features',
            'site',
            'test',
            'data-provider',
            'map',
            'twitter'
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
