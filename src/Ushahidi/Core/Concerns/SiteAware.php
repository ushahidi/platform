<?php

namespace Ushahidi\Core\Concerns;

use Ushahidi\Core\Facade\Site;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use ReflectionClass;
use ReflectionProperty;

trait SiteAware
{
    /**
     * @var int The hostname ID of the previously active deployment
     */
    protected $site;
    use SerializesAndRestoresModelIdentifiers;


    public function __sleep()
    {
        $this->site = Site::instance()->getId();

        $properties = (new ReflectionClass($this))->getProperties();

        foreach ($properties as $property) {
            $property->setValue($this, $this->getSerializedPropertyValue(
                $this->getPropertyValue($property)
            ));
        }

        return array_values(array_filter(array_map(function ($p) {
            return $p->isStatic() ? null : $p->getName();
        }, $properties)));
    }

    public function __wakeup()
    {
        if (isset($this->site) && $this->site) {
            Event::dispatch('site.restored', ['site' => $this->site]);
        }
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $property->setValue($this, $this->getRestoredPropertyValue(
                $this->getPropertyValue($property)
            ));
        }
    }

    /**
     * Get the property value for the given property.
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($this);
    }
}
