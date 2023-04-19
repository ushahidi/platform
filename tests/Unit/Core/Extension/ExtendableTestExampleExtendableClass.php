<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class that has extensions enabled
 */
class ExtendableTestExampleExtendableClass extends Extendable
{
    public $behaviors = [ExtendableTestExampleBehaviorClass1::class];

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
