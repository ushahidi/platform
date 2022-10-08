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
use Ushahidi\Core\Concerns\DeriveData;
use Ushahidi\Core\Concerns\DefaultData;
use Ushahidi\Core\Concerns\TransformData;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class EloquentEntity extends EloquentModel implements Entity
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
            $this->prepareEntityData($this->addDefaultDataToArray($attributes ?? []))
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

    // public function getCasts()
    // {
    //     if (method_exists($this, 'getDefinition')) {
    //         return array_merge($this->getDefinition(), parent::getCasts());
    //     }

    //     return parent::getCasts();
    // }

    public function getResource()
    {
        return $this->resource;
    }

    public function setState(array $data)
    {
        $this->syncOriginal();

        $this->fill($this->prepareEntityData($data));

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
        // return $this->getAttributes();

        // return $this->attributesToArray();

        // return get_object_vars($this)['attributes'];

        // So basically get all properties and iterate through them
        // checking if they were declared in class we reflected.

        $class = new \ReflectionClass($this);

        $properties = array_filter($class->getProperties(), function(\ReflectionProperty $prop) use($class){
            return $prop->getDeclaringClass()->getName() == $class->getName();
        });

        if (empty($properties)) {
            return $this->attributesToArray();
        }

        return $this->only(Collection::make($properties)->pluck('name')->toArray());
    }

    public function jsonSerialize()
    {
        return $this->asArray();
    }

    protected function prepareEntityData($data)
    {
        // Get the immutable values. Once set, these cannot be changed.
        $immutable = $this->getImmutable();

        $filtered = Collection::make($this->derive($data))->filter(
            function($value, $key) use ($immutable)  {
                if (in_array($key, $immutable) && isset($this->original[$key])) {
                    // Value has already been set and cannot be changed.
                    return false;
                }
                return true;
            }
        )->toArray();

        return $this->transform($filtered);
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

    // protected function asDateTime($value)
    // {
    //     $value = parent::asDateTime($value);

    //     return $value->toDateTime();
    // }

    // protected function castAttribute($key, $value)
    // {
    //     if($func = $this->getCustomTransformer($this->getCastType($key))) {
    //         return static::$func($value);
    //     }

    //     return parent::castAttribute($key, $value);
    // }
}
