<?php
namespace Ushahidi\Core\Eloquent;

/**
 * @property-read \Illuminate\Database\Eloquent\Model $model
 * @see \Ushahidi\Contracts\Entity
 */
trait HasState
{
    public function setState(array $data)
    {
        if (empty($this->original)) {
            $this->syncOriginal();
        }

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
}
