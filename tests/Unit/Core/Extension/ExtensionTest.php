<?php

use Ushahidi\Core\Extension\Extendable;
use Ushahidi\Core\Extension\ExtensionBase;
use Ushahidi\Tests\TestCase;

class ExtensionTest extends TestCase
{
    public function testExtendingBehavior()
    {
        $subject = new ExtensionTestExampleExtendableClass;
        $this->assertEquals('foo', $subject->behaviorAttribute);

        ExtensionTestExampleBehaviorClass1::extend(function ($extension) {
            $extension->behaviorAttribute = 'bar';
        });

        $subject = new ExtensionTestExampleExtendableClass;
        $this->assertEquals('bar', $subject->behaviorAttribute);
    }
}

/*
 * Example class that has extensions enabled
 */
class ExtensionTestExampleExtendableClass extends Extendable
{
    public $implement = ['ExtensionTestExampleBehaviorClass1'];
}

/**
 * Example behavior classes
 */
class ExtensionTestExampleBehaviorClass1 extends ExtensionBase
{
    public $behaviorAttribute = 'foo';
}
