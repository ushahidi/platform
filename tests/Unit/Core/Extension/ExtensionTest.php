<?php
namespace Tests\Unit\Core\Extension;

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

