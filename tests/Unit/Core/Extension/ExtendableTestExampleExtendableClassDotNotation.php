<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class that has extensions enabled using dot notation
 */
class ExtendableTestExampleExtendableClassDotNotation extends Extendable
{
    public $behaviors = ['ExtendableTest.ExampleBehaviorClass1'];

    public $classAttribute;

    protected $protectedFoo = 'bar';

    public static function vanillaIceIce()
    {
        return 'baby';
    }

    protected function protectedBar()
    {
        return 'foo';
    }

    protected static function protectedMars()
    {
        return 'bar';
    }

    public function getProtectedFooAttribute()
    {
        return $this->protectedFoo;
    }
}
