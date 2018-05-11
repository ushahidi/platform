<?php

namespace Tests\Unit\Core\Entity;

use Ushahidi\Core\Entity;

/**
 * Ushahidi Export Job
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class MockExportJobEntity implements Entity
{

    use \Ushahidi\Core\Traits\StatefulData;

    public $id;
    public $entity_type;
    public $user_id;
    public $fields;
    public $filters;
    public $status;
    public $url;
    public $header_row;
    public $created;
    public $updated;
    public $url_expiration;

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
    }

    public function __isset($key)
    {
        return property_exists($this, $key);
    }

    public function asArray()
    {
        return get_object_vars($this);
    }

    public function setStateValue($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    // StatefulData
    public function getDerived()
    {
        // Foreign key alias
        return [
            'user_id' => ['user', 'user.id']
        ];
    }

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id' => 'int',
            'entity_type' => 'string',
            'user_id' => 'int',
            'status' => 'string',
            'url' => 'string',
            'fields' => '*json',
            'filters' => '*json',
            'header_row' => '*json',
            'created' => 'int',
            'updated' => 'int',
            'url_expiration' => 'int'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'export_job';
    }

    // StatefulData
    public function getImmutable()
    {
        return ['user_id'];
    }

    /**
     * Return the unique ID for the entity.
     *
     * @return Mixed
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}
