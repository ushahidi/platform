<?php
namespace Ushahidi\Core\Extension;

/**
 * Extendable class
 *
 * If a class extends this class, it will enable support for using "Private traits".
 *
 * Usage:
 *
 *     public $implement = [\Path\To\Some\Namespace\Class::class];
 *
 * See the `ExtensionBase` class for creating extension classes.
 *
 */
class Extendable
{
    use ExtendableTrait;

    /**
     * @var array Behaviours used by this class.
     */
    public $behaviors = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extendableConstruct();
    }

    public function __get($name)
    {
        return $this->extendableGet($name);
    }

    public function __set($name, $value)
    {
        $this->extendableSet($name, $value);
    }

    public function __call($name, $params)
    {
        return $this->extendableCall($name, $params);
    }

    public static function __callStatic($name, $params)
    {
        return self::extendableCallStatic($name, $params);
    }

    public static function extend(callable $callback)
    {
        self::extendableExtendCallback($callback);
    }
}
