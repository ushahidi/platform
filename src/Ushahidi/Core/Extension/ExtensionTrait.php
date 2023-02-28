<?php
namespace Ushahidi\Core\Extension;

/**
 * ExtensionTrait allows for "private traits"
 *
 * @package ushahidi\extension
 * @see Ushahidi\Core\Extension\ExtensionBase
 */
trait ExtensionTrait
{
    /**
     * @var array Used to extend the constructor of an extension class. Eg:
     *
     *     BehaviorClass::extend(function($obj) { })
     *
     */
    protected static $extensionCallbacks = [];

    /**
     * @var string The calling class when using a static method.
     */
    public static $extendableStaticCalledClass = null;

    protected $extensionHidden = [
        'fields' => [],
        'methods' => ['extensionIsHiddenField', 'extensionIsHiddenMethod']
    ];

    public function extensionApplyInitCallbacks()
    {
        $classes = array_merge([get_class($this)], class_parents($this));
        foreach ($classes as $class) {
            if (isset(self::$extensionCallbacks[$class]) && is_array(self::$extensionCallbacks[$class])) {
                foreach (self::$extensionCallbacks[$class] as $callback) {
                    call_user_func($callback, $this);
                }
            }
        }
    }

    /**
     * Helper method for `::extend()` static method
     * @param  callable $callback
     * @return void
     */
    public static function extensionExtendCallback($callback)
    {
        $class = get_called_class();
        if (!isset(self::$extensionCallbacks[$class]) ||
            !is_array(self::$extensionCallbacks[$class])
        ) {
            self::$extensionCallbacks[$class] = [];
        }

        self::$extensionCallbacks[$class][] = $callback;
    }

    protected function extensionHideField($name)
    {
        $this->extensionHidden['fields'][] = $name;
    }

    protected function extensionHideMethod($name)
    {
        $this->extensionHidden['methods'][] = $name;
    }

    public function extensionIsHiddenField($name)
    {
        return in_array($name, $this->extensionHidden['fields']);
    }

    public function extensionIsHiddenMethod($name)
    {
        return in_array($name, $this->extensionHidden['methods']);
    }

    public static function getCalledExtensionClass()
    {
        return self::$extendableStaticCalledClass;
    }
}
