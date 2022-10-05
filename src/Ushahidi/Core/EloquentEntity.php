<?php

/**
 * Ushahidi Platform Eloquent Model
 *
 * Gives eloquent models compatibility with clean architecture entity:
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core;

use Ushahidi\Contracts\Entity;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class EloquentEntity extends Eloquent implements Entity
{
    /**
     * The resource name for this entity.
     *
     * @var string
     */
    public $resource;

    public function get($id)
    {
        return self::query()->find($id);
    }

    public function getId()
    {
        return $this->getKey();
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setState(array $data)
    {
        $this->fill($data);

        return $this;
    }

    public function hasChanged($key, $array_key = null)
    {
        return $this->isDirty($key);
    }

    public function getChanged()
    {
        return $this->getDirty();
    }

    public function asArray()
    {
        return $this->attributesToArray();
    }
}
