## Extensions

Adds the ability for classes to have *private traits*, also known as Behaviors. These are similar to native PHP Traits except they have some distinct benefits:

1. Behaviors have their own constructor.
1. Behaviors can have private or protected methods.
1. Methods and property names can conflict safely.
1. Class can be extended with behaviors dynamically.

Where you might use a trait like this:

    class MyClass
    {
        use \Ushahidi\Core\UtilityFunctions;
        use \Ushahidi\Core\DeferredBinding;
    }

A behavior is used in a similar fashion:

    class MyClass extends \Ushahidi\Core\Extension\Extendable
    {
        public $implement = [
            'Ushahidi.Core.UtilityFunctions',
            'Ushahidi.Core.DeferredBinding',
        ];
    }

Where you might define a trait like this:

    trait UtilityFunctions
    {
        public function sayHello()
        {
            echo "Hello from " . get_class($this);
        }
    }

A behavior is defined like this:

    class UtilityFunctions extends \Ushahidi\Core\Extension\ExtensionBase
    {
        protected $parent;

        public function __construct($parent)
        {
            $this->parent = $parent;
        }

        public function sayHello()
        {
            echo "Hello from " . get_class($this->parent);
        }
    }

The extended object is always passed as the first parameter to the Behavior's constructor.

### Usage example

#### Behavior / Extension class

    <?php namespace MyNamespace\Behaviors;

    class FormController extends \Ushahidi\Core\Extension\ExtensionBase
    {
        /**
         * @var Reference to the extended object.
         */
        protected $controller;

        /**
         * Constructor
         */
        public function __construct($controller)
        {
            $this->controller = $controller;
        }

        public function someMethod()
        {
            return "I come from the FormController Behavior!";
        }

        public function otherMethod()
        {
            return "You might not see me...";
        }
    }

#### Extending a class

This `Controller` class will implement the `FormController` behavior and then the methods will become available (mixed in) to the class. We will override the `otherMethod` method.

    <?php namespace MyNamespace;

    class Controller extends \Ushahidi\Core\Extension\Extendable
    {

        /**
         * Implement the FormController behavior
         */
        public $implement = [
            'MyNamespace.Behaviors.FormController'
        ];

        public function otherMethod()
        {
            return "I come from the main Controller!";
        }
    }

#### Using the extension

    $controller = new MyNamespace\Controller;

    // Prints: I come from the FormController Behavior!
    echo $controller->someMethod();

    // Prints: I come from the main Controller!
    echo $controller->otherMethod();

    // Prints: You might not see me...
    echo $controller->asExtension('FormController')->otherMethod();

### Dynamically using a behavior / Constructor extension

Any class that uses the `Extendable` or `ExtendableTrait` can have its constructor extended with the static `extend()` method. The argument should pass a closure that will be called as part of the class constructor. For example:

    /**
     * Extend the Pizza Shop to include the Master Splinter behavior too
     */
    MyNamespace\Controller::extend(function($controller){

        // Implement the list controller behavior dynamically
        $controller->implement[] = 'MyNamespace.Behaviors.ListController';
    });

### Dynamically creating methods
Methods can be added to a `Model` through the use of `addDynamicMethod`.

    Post::extend(function($model) {
        $model->addDynamicMethod('getTagsAttribute', function() use ($model) {
            return $model->tags()->lists('name');
        });
    });

### Soft definition

If a behavior class does not exist, like a trait, an *Class not found* error will be thrown. In some cases you may wish to suppress this error, for conditional implementation if a module is present in the system. You can do this by placing an `@` symbol at the beginning of the class name.

    class User extends \Ushahidi\Core\Extension\Extendable
    {
        public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];
    }

If the class name `RainLab\Translate\Behaviors\TranslatableModel` does not exist, no error will be thrown. This is the equivalent of the following code:

    class User extends \Ushahidi\Core\Extension\Extendable
    {
        public $implement = [];

        public function __construct()
        {
            if (class_exists('RainLab\Translate\Behaviors\TranslatableModel')) {
                $controller->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
            }

            parent::__construct();
        }
    }

### Using Traits instead of base classes

In some cases you may not wish to extend the `ExtensionBase` or `Extendable` classes, due to other needs. So you can use the traits instead, although obviously the behavior methods will not be available to the parent class.

- When using the `ExtensionTrait` the methods from `ExtensionBase` should be applied to the class.

- When using the `ExtendableTrait` the methods from `Extendable` should be applied to the class.
