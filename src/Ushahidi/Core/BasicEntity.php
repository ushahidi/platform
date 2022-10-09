<?php

/**
 * Ushahidi Platform Basic Entity
 *
 * Dynamic entities have unknown properties and can be mutated to any structure.
 * Object properties are faked through an internal storage array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\StatefulData;

abstract class BasicEntity implements Entity
{
    use StatefulData;

    /**
     * The entity's attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * Transparent access to dynamic entity properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Transparent checking of dynamic entity properties.
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    // StatefulData
    protected function setStateValue($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function asArray()
    {
        return $this->attributes;
    }

    public function getId()
    {
        return $this->attributes['id'] ?? null;
    }

    protected function getImmutable()
    {
        return ['id', 'created'];
    }
}
