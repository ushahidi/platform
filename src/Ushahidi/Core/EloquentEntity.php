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

use Illuminate\Support\Arr;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Concerns\DeriveData;
use Illuminate\Database\Eloquent\Model;
use Ushahidi\Core\Concerns\DefaultData;
use Ushahidi\Core\Concerns\TransformData;

abstract class EloquentEntity extends Model implements Entity
{
    use DefaultData, DeriveData, TransformData;

    /**
     * The resource name for this entity.
     *
     * @var string
     */
    protected $resource;

    protected $guarded = [];

    public function __construct(array $attributes = null)
    {
        parent::__construct(
            $this->getEntityData(
                $this->addDefaultDataToArray($attributes ?? [])
            )
        );
    }

    public function get($id)
    {
        return self::query()->find($id);
    }

    public function getId()
    {
        return $this->getAttributeFromArray($this->getKeyName());
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setState(array $data)
    {
        if (empty($this->original)) {
            $this->syncOriginal();
        }

        $this->fill($this->getEntityData($data));

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

    //
    public function asArray()
    {
        if (empty($properties = $this->getEntityProperties())) {
            // return $this->getAttributes();

            return $this->attributesToArray();
        }

        return $this->only($properties);
    }

    public function jsonSerialize()
    {
        return $this->asArray();
    }

    protected function getEntityData(array $data)
    {
        // Get the immutable values. Once set, these cannot be changed.
         $immutable = $this->getImmutable();

        $filtered = Arr::where($this->derive($data), function ($value, $key) use ($immutable) {
            if (in_array($key, $immutable) && isset($this->original[$key])) {
                // Value has already been set and cannot be changed.
                return false;
            }
            return true;
        });

        return $this->transform($filtered);
    }

    protected function getEntityProperties()
    {
        // So basically get all properties and iterate through them
        // checking if they were declared in class we reflected.
        $getProperties = function (\ReflectionClass $class) {
            return array_filter($class->getProperties(), function (\ReflectionProperty $prop) use ($class) {
                return $prop->getDeclaringClass()->getName() == $class->getName() && $prop->isProtected();
            });
        };

        $class = new \ReflectionClass($this);

        $properties = $getProperties($class);

        // In the case an entity that extends a parent entity,
        // we should be able to get the properties of the parent class and
        // merge it with the child class.
        //
        // We're specifically checking if the parent class isn't abstract,
        // as we don't want the properties of the base entity class with data logic
        // i.e Eloquent properties
        if (($parentClass = $class->getParentClass()) && !$parentClass->isAbstract()) {
            $properties = array_merge($properties, $getProperties($parentClass));
        }

        return Arr::pluck($properties, 'name');
    }

    protected function getDerived()
    {
        return [];
    }

    /**
     * Return the names of values that cannot be modified once set.
     *
     * @return array
     */
    protected function getImmutable()
    {
        return ['id', 'created'];
    }
}
