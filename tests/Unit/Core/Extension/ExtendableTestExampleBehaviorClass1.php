<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\ExtensionBase;

/**
 * Example behavior classes
 */
class ExtendableTestExampleBehaviorClass1 extends ExtensionBase
{
    public $behaviorAttribute;

    public function getFoo()
    {
        return 'foo';
    }

    public static function getStaticBar()
    {
        return 'bar';
    }

    public static function vanillaIceIce()
    {
        return 'cream';
    }

    public function hasPanda()
    {
        return true;
    }
}
