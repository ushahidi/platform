<?php
namespace Ushahidi\Core\Extension;

use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use BadMethodCallException;
use Exception;

/**
 * ExtendableTrait trait is used when access to the underlying base class
 * is not available, such as classes that belong to the foundation
 * framework (Laravel).
 *
 * @see \Ushahidi\Core\Extension\Extendable
 */
trait ExtendableTrait
{
    /**
     * @var array extensionData contains class reflection information, including behaviors
     */
    protected $extensionData = [
        'extensions' => [],
        'methods' => [],
        'dynamicMethods' => [],
        'dynamicProperties' => []
    ];

    /**
     * @var array extendableCallbacks is used to extend the constructor of an extendable class. Eg:
     *
     *     Class::extend(function($obj) { })
     *
     */
    protected static $extendableCallbacks = [];

    /**
     * @var array extendableStaticMethods is a collection of static methods used by behaviors
     */
    protected static $extendableStaticMethods = [];

    /**
     * @var bool extendableGuardProperties indicates if dynamic properties can be created
     */
    protected static $extendableGuardProperties = true;

    /**
     * extendableConstruct should be called as part of the constructor
     */
    public function extendableConstruct()
    {
        /*
         * Apply init callbacks
         */
        $classes = array_merge([get_class($this)], class_parents($this));
        foreach ($classes as $class) {
            if (isset(self::$extendableCallbacks[$class]) && is_array(self::$extendableCallbacks[$class])) {
                foreach (self::$extendableCallbacks[$class] as $callback) {
                    call_user_func($callback, $this);
                }
            }
        }

        /*
         * Apply extensions
         */
        foreach ($this->extensionExtractBehaviors() as $useClass) {
            /*
             * Soft implement
             */
            if (substr($useClass, 0, 1) === '@') {
                $useClass = substr($useClass, 1);
                if (!class_exists($useClass)) {
                    continue;
                }
            }

            $this->extendClassWith($useClass);
        }
    }

    /**
     * extendableExtendCallback is a helper method for `::extend()` static method
     * @param  callable $callback
     * @return void
     */
    public static function extendableExtendCallback($callback)
    {
        $class = get_called_class();
        if (!isset(self::$extendableCallbacks[$class]) ||
            !is_array(self::$extendableCallbacks[$class])
        ) {
            self::$extendableCallbacks[$class] = [];
        }

        self::$extendableCallbacks[$class][] = $callback;
    }

    /**
     * clearExtendedClasses clears the list of extended classes so they will be re-extended
     */
    public static function clearExtendedClasses()
    {
        self::$extendableCallbacks = [];
    }

    /**
     * extensionExtractBehaviors will return classes to implement.
     */
    protected function extensionExtractBehaviors(): array
    {
        if (!$this->behaviors) {
            return [];
        }

        if (is_string($this->behaviors)) {
            $uses = explode(',', $this->behaviors);
        } elseif (is_array($this->behaviors)) {
            $uses = $this->behaviors;
        } else {
            throw new Exception(sprintf('Class %s contains an invalid $behaviors value', get_class($this)));
        }

        foreach ($uses as &$use) {
            $use = str_replace('.', '\\', trim($use));
        }

        return $uses;
    }

    /**
     * extensionExtractMethods extracts the available methods from a behavior and adds it
     * to the list of callable methods
     * @param  string $extensionName
     * @param  object $extensionObject
     * @return void
     */
    protected function extensionExtractMethods($extensionName, $extensionObject)
    {
        if (!method_exists($extensionObject, 'extensionIsHiddenMethod')) {
            throw new Exception(
                sprintf(
                    'Extension %s should inherit Ushahidi\Core\Extension\ExtensionBase or $this->behaviors Ushahidi\Core\Extension\ExtensionTrait.',
                    $extensionName
                )
            );
        }

        $extensionMethods = get_class_methods($extensionName);
        foreach ($extensionMethods as $methodName) {
            if ($methodName === '__construct' ||
                $extensionObject->extensionIsHiddenMethod($methodName)
            ) {
                continue;
            }

            $this->extensionData['methods'][$methodName] = $extensionName;
        }
    }

    /**
     * addDynamicMethod programmatically adds a method to the extendable class
     * @param string   $dynamicName
     * @param callable $method
     * @param string   $extension
     */
    public function addDynamicMethod($dynamicName, $method, $extension = null)
    {
        if (is_string($method) &&
            $extension &&
            ($extensionObj = $this->getClassExtension($extension))
        ) {
            $method = [$extensionObj, $method];
        }

        $this->extensionData['dynamicMethods'][$dynamicName] = $method;
    }

    /**
     * addDynamicProperty programmatically adds a property to the extendable class
     * @param string $dynamicName
     * @param string $value
     */
    public function addDynamicProperty($dynamicName, $value = null)
    {
        if (property_exists($this, $dynamicName)) {
            return;
        }

        self::$extendableGuardProperties = false;

        $this->{$dynamicName} = $value;

        self::$extendableGuardProperties = true;

        $this->extensionData['dynamicProperties'][] = $dynamicName;
    }

    /**
     * extendableIsSettingDynamicProperty returns true if a dynamic
     * property action is taking place
     */
    protected function extendableIsSettingDynamicProperty(): bool
    {
        return self::$extendableGuardProperties === false;
    }

    /**
     * extendClassWith dynamically extends a class with a specified behavior
     * @param  string $extensionName
     * @return void
     */
    public function extendClassWith($extensionName)
    {
        if (!strlen($extensionName)) {
            return;
        }

        $extensionName = str_replace('.', '\\', trim($extensionName));

        if (isset($this->extensionData['extensions'][$extensionName])) {
            throw new Exception(
                sprintf(
                    'Class %s has already been extended with %s',
                    get_class($this),
                    $extensionName
                )
            );
        }

        $this->extensionData['extensions'][$extensionName] = $extensionObject = new $extensionName($this);
        $this->extensionExtractMethods($extensionName, $extensionObject);
        $extensionObject->extensionApplyInitCallbacks();
    }

    /**
     * isClassExtendedWith checks if extendable class is extended with a behavior object
     * @param  string $name Fully qualified behavior name
     * @return boolean
     */
    public function isClassExtendedWith($name)
    {
        $name = str_replace('.', '\\', trim($name));
        return isset($this->extensionData['extensions'][$name]);
    }

    /**
     * implementClassWith will implement an extension using non-interference and should
     * be used with the static extend() method.
     */
    public function implementClassWith($extensionName)
    {
        $extensionName = str_replace('.', '\\', trim($extensionName));

        if (in_array($extensionName, $this->extensionExtractBehaviors())) {
            return;
        }

        $this->behaviors[] = $extensionName;
    }

    /**
     * isClassInstanceOf checks if the class implements the supplied interface methods.
     */
    public function isClassInstanceOf($interface): bool
    {
        $classMethods = $this->getClassMethods();

        if (is_string($interface) && !interface_exists($interface)) {
            throw new Exception(
                sprintf(
                    'Interface %s does not exist',
                    $interface
                )
            );
        }

        $interfaceMethods = (array) get_class_methods($interface);
        foreach ($interfaceMethods as $methodName) {
            if (!in_array($methodName, $classMethods)) {
                return false;
            }
        }

        return true;
    }

    /**
     * getClassExtension returns a behavior object from an extendable class, example:
     *
     *     $this->getClassExtension('Backend.Behaviors.FormController')
     *
     * @param  string $name Fully qualified behavior name
     * @return mixed
     */
    public function getClassExtension($name)
    {
        $name = str_replace('.', '\\', trim($name));
        return $this->extensionData['extensions'][$name] ?? null;
    }

    /**
     * asExtension is short hand for `getClassExtension()` method, except takes the short
     * extension name, example:
     *
     *     $this->asExtension('FormController')
     *
     * @param  string $shortName
     * @return mixed
     */
    public function asExtension($shortName)
    {
        foreach ($this->extensionData['extensions'] as $class => $obj) {
            if (preg_match('@\\\\([\w]+)$@', $class, $matches) &&
                $matches[1] === $shortName
            ) {
                return $obj;
            }
        }

        return $this->getClassExtension($shortName);
    }

    /**
     * methodExists checks if a method exists, extension equivalent of method_exists()
     * @param  string $name
     * @return boolean
     */
    public function methodExists($name)
    {
        return (
            method_exists($this, $name) ||
            isset($this->extensionData['methods'][$name]) ||
            isset($this->extensionData['dynamicMethods'][$name])
        );
    }

    /**
     * getClassMethods gets a list of class methods, extension equivalent of get_class_methods()
     * @return array
     */
    public function getClassMethods()
    {
        return array_values(array_unique(
            array_merge(
                get_class_methods($this),
                array_keys($this->extensionData['methods']),
                array_keys($this->extensionData['dynamicMethods'])
            )
        ));
    }

    /**
     * getClassMethodAsReflector
     */
    public function getClassMethodAsReflector(string $name): ReflectionFunctionAbstract
    {
        $extandableMethod = $this->getExtendableMethodFromExtensions($name);
        if ($extandableMethod !== null) {
            return new ReflectionMethod($extandableMethod[0], $extandableMethod[1]);
        }

        $extandableDynamicMethod = $this->getExtendableMethodFromDynamicMethods($name);
        if ($extandableDynamicMethod !== null) {
            return new ReflectionFunction($extandableDynamicMethod);
        }

        return new ReflectionMethod($this, $name);
    }

    /**
     * getDynamicProperties returns all dynamic properties and their values
     * @return array ['property' => 'value']
     */
    public function getDynamicProperties()
    {
        $result = [];

        foreach ($this->extensionData['dynamicProperties'] as $propName) {
            $result[$propName] = $this->{$propName};
        }

        return $result;
    }

    /**
     * propertyExists checks if a property exists, extension equivalent of `property_exists()`
     * @param  string $name
     * @return boolean
     */
    public function propertyExists($name)
    {
        if (property_exists($this, $name)) {
            return true;
        }

        foreach ($this->extensionData['extensions'] as $extensionObject) {
            if (property_exists($extensionObject, $name) &&
                $this->extendableIsAccessible($extensionObject, $name)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * extendableIsAccessible checks if a property is accessible, property equivalent
     * of `is_callable()`
     * @param  mixed  $class
     * @param  string $propertyName
     * @return boolean
     */
    protected function extendableIsAccessible($class, $propertyName)
    {
        $reflector = new ReflectionClass($class);
        $property = $reflector->getProperty($propertyName);
        return $property->isPublic();
    }

    /**
     * extendableGet magic method for `__get()`
     * @param  string $name
     * @return string|void
     */
    public function extendableGet($name)
    {
        foreach ($this->extensionData['extensions'] as $extensionObject) {
            if (property_exists($extensionObject, $name) &&
                $this->extendableIsAccessible($extensionObject, $name)
            ) {
                return $extensionObject->{$name};
            }
        }

        $parent = get_parent_class();
        if ($parent !== false && method_exists($parent, '__get')) {
            return parent::__get($name);
        }
    }

    /**
     * extendableSet magic method for `__set()`
     * @param  string $name
     * @param  string $value
     * @return string|void
     */
    public function extendableSet($name, $value)
    {
        $found = false;

        // Spin over each extension to find it
        foreach ($this->extensionData['extensions'] as $extensionObject) {
            if (!property_exists($extensionObject, $name)) {
                continue;
            }

            $extensionObject->{$name} = $value;
            $found = true;
        }

        // Setting an undefined property, magic ends here since the property now exists
        if (!self::$extendableGuardProperties) {
            $this->{$name} = $value;
            return;
        }

        // This targets trait usage in particular
        $parent = get_parent_class();
        if ($parent !== false && method_exists($parent, '__set')) {
            parent::__set($name, $value);
            $found = true;
        }

        // Undefined property, throw an exception to catch it,
        // otherwise some PHP versions will segfault
        // @todo Restore if year >= 2023
        // if (!$found) {
        //     throw new BadMethodCallException(sprintf(
        //         'Call to undefined property %s::%s',
        //         get_class($this),
        //         $name
        //     ));
        // }
    }

    /**
     * extendableCall magic method for `__call()`
     * @param  string $name
     * @param  array  $params
     * @return mixed
     */
    public function extendableCall($name, $params = null)
    {
        $callable = $this->getExtendableMethodFromExtensions($name);

        if ($callable === null) {
            $callable = $this->getExtendableMethodFromDynamicMethods($name);
        }

        if ($callable !== null) {
            return call_user_func_array($callable, $params);
        }

        $parent = get_parent_class();
        if ($parent !== false && method_exists($parent, '__call')) {
            return parent::__call($name, $params);
        }

        throw new BadMethodCallException(
            sprintf(
                'Call to undefined method %s::%s()',
                get_class($this),
                $name
            )
        );
    }

    /**
     * extendableCallStatic magic method for `__callStatic()`
     * @param  string $name
     * @param  array  $params
     * @return mixed
     */
    public static function extendableCallStatic($name, $params = null)
    {
        $className = get_called_class();

        if (!array_key_exists($className, self::$extendableStaticMethods)) {
            self::$extendableStaticMethods[$className] = [];

            $class = new ReflectionClass($className);
            $defaultProperties = $class->getDefaultProperties();
            if (array_key_exists('behaviors', $defaultProperties) &&
                ($behaviors = $defaultProperties['behaviors'])
            ) {
                /*
                 * Apply extensions
                 */
                if (is_string($behaviors)) {
                    $uses = explode(',', $behaviors);
                } elseif (is_array($behaviors)) {
                    $uses = $behaviors;
                } else {
                    throw new Exception(sprintf('Class %s contains an invalid $behaviors value', $className));
                }

                foreach ($uses as $use) {
                    $useClassName = str_replace('.', '\\', trim($use));

                    $useClass = new ReflectionClass($useClassName);
                    $staticMethods = $useClass->getMethods(ReflectionMethod::IS_STATIC);
                    foreach ($staticMethods as $method) {
                        self::$extendableStaticMethods[$className][$method->getName()] = $useClassName;
                    }
                }
            }
        }

        if (isset(self::$extendableStaticMethods[$className][$name])) {
            $extension = self::$extendableStaticMethods[$className][$name];

            if (method_exists($extension, $name) && is_callable([$extension, $name])) {
                $extension::$extendableStaticCalledClass = $className;
                $result = forward_static_call_array([$extension, $name], $params);
                $extension::$extendableStaticCalledClass = null;
                return $result;
            }
        }

        // $parent = get_parent_class($className);
        // if ($parent !== false && method_exists($parent, '__callStatic')) {
        //    return parent::__callStatic($name, $params);
        // }

        throw new BadMethodCallException(
            sprintf(
                'Call to undefined method %s::%s()',
                $className,
                $name
            )
        );
    }

    /**
     * getExtendableMethodFromExtensions
     */
    protected function getExtendableMethodFromExtensions(string $name): ?array
    {
        if (!isset($this->extensionData['methods'][$name])) {
            return null;
        }

        $extension = $this->extensionData['methods'][$name];
        $extensionObject = $this->extensionData['extensions'][$extension];

        if (!method_exists($extension, $name) || !is_callable([$extensionObject, $name])) {
            return null;
        }

        return [$extensionObject, $name];
    }

    /**
     * getExtendableMethodFromDynamicMethods
     */
    protected function getExtendableMethodFromDynamicMethods(string $name): ?callable
    {
        if (!isset($this->extensionData['dynamicMethods'][$name])) {
            return null;
        }

        $dynamicCallable = $this->extensionData['dynamicMethods'][$name];

        if (!is_callable($dynamicCallable)) {
            return null;
        }

        return $dynamicCallable;
    }
}
