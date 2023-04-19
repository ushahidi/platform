<?php
// @codingStandardsIgnoreFile

namespace Tests\Unit\Core\Extension;

/*
 * Add namespaced aliases for dot notation test
 */
class_alias(ExtendableTestExampleBehaviorClass1::class, 'ExtendableTest\\ExampleBehaviorClass1');
class_alias(ExtendableTestExampleBehaviorClass2::class, 'ExtendableTest\\ExampleBehaviorClass2');

use Ushahidi\Tests\TestCase;

class ExtendableTest extends TestCase
{
    public function testExtendingExtendableClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $this->assertNull($subject->classAttribute);

        ExtendableTestExampleExtendableClass::extend(function ($extension) {
            $extension->classAttribute = 'bar';
        });

        $subject = new ExtendableTestExampleExtendableClass;
        $this->assertEquals('bar', $subject->classAttribute);
    }

    public function testSettingDeclaredPropertyOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->classAttribute = 'Test';
        $this->assertEquals('Test', $subject->classAttribute);
    }

    // public function testSettingUndeclaredPropertyOnClass()
    // {
    //     $this->expectException(\BadMethodCallException::class);
    //     $this->expectExceptionMessage("Call to undefined property
    //       ExtendableTestExampleExtendableClass::newAttribute"
    //     );

    //     $subject = new ExtendableTestExampleExtendableClass;
    //     $subject->newAttribute = 'Test';
    // }

    public function testSettingDeclaredPropertyOnBehavior()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $behavior = $subject->getClassExtension(ExtendableTestExampleBehaviorClass1::class);

        $subject->behaviorAttribute = 'Test';
        $this->assertEquals('Test', $subject->behaviorAttribute);
        $this->assertEquals('Test', $behavior->behaviorAttribute);
        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
    }

    public function testDynamicPropertyOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $this->assertFalse(property_exists($subject, 'newAttribute'));
        $subject->addDynamicProperty('dynamicAttribute', 'Test');
        $this->assertEquals('Test', $subject->dynamicAttribute);
        $this->assertTrue(property_exists($subject, 'dynamicAttribute'));
    }

    public function testDynamicallyImplementingClass()
    {
        ExtendableTestExampleImplementableClass::extend(function ($obj) {
            $obj->implementClassWith(ExtendableTestExampleBehaviorClass2::class);
            $obj->implementClassWith(ExtendableTestExampleBehaviorClass2::class);
            $obj->implementClassWith(ExtendableTestExampleBehaviorClass2::class);
        });

        $subject = new ExtendableTestExampleImplementableClass;
        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass2::class));
    }

    public function testDynamicallyExtendingClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->extendClassWith(ExtendableTestExampleBehaviorClass2::class);

        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass2::class));
    }

    public function testDynamicMethodOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->addDynamicMethod('getFooAnotherWay', 'getFoo', ExtendableTestExampleBehaviorClass1::class);

        $this->assertEquals('foo', $subject->getFoo());
        $this->assertEquals('foo', $subject->getFooAnotherWay());
    }

    public function testDynamicExtendAndMethodOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->extendClassWith(ExtendableTestExampleBehaviorClass2::class);
        $subject->addDynamicMethod('getOriginalFoo', 'getFoo', ExtendableTestExampleBehaviorClass1::class);

        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
        $this->assertTrue($subject->isClassExtendedWith(ExtendableTestExampleBehaviorClass2::class));
        $this->assertEquals('bar', $subject->getFoo());
        $this->assertEquals('foo', $subject->getOriginalFoo());
    }

    public function testDynamicClosureOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->addDynamicMethod('sayHello', function () {
            return 'Hello world';
        });

        $this->assertEquals('Hello world', $subject->sayHello());
    }

    public function testDynamicCallableOnClass()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->addDynamicMethod('getAppName', [ExtendableTestExampleClass::class, 'getName']);

        $this->assertEquals('ushahidi', $subject->getAppName());
    }

    public function testCallingStaticMethod()
    {
        $result = ExtendableTestExampleExtendableClass::getStaticBar();
        $this->assertEquals('bar', $result);

        $result = ExtendableTestExampleExtendableClass::vanillaIceIce();
        $this->assertEquals('baby', $result);
    }

    public function testCallingUndefinedStaticMethod()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Call to undefined method ' .
            ExtendableTestExampleExtendableClass::class . '::undefinedMethod()'
        );

        $result = ExtendableTestExampleExtendableClass::undefinedMethod();
        $this->assertEquals('bar', $result);
    }

    public function testAccessingProtectedMethod()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Call to undefined method ' .
            ExtendableTestExampleExtendableClass::class . '::protectedBar()'
        );

        $subject = new ExtendableTestExampleExtendableClass;
        echo $subject->protectedBar();
    }

    public function testAccessingProtectedStaticMethod()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Call to undefined method ' .
            ExtendableTestExampleExtendableClass::class . '::protectedMars()'
        );

        echo ExtendableTestExampleExtendableClass::protectedMars();
    }

    public function testInvalidImplementValue()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Class ' .
            ExtendableTestInvalidExtendableClass::class
            . ' contains an invalid $behaviors value'
        );

        $result = new ExtendableTestInvalidExtendableClass;
    }

    public function testSoftImplementFake()
    {
        $result = new ExtendableTestExampleExtendableSoftImplementFakeClass;
        $this->assertFalse($result->isClassExtendedWith('RabbleRabbleRabble'));
        $this->assertEquals('working', $result->getStatus());
    }

    public function testSoftImplementReal()
    {
        $result = new ExtendableTestExampleExtendableSoftImplementRealClass;
        $this->assertTrue($result->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
        $this->assertEquals('foo', $result->getFoo());
    }

    public function testSoftImplementCombo()
    {
        $result = new ExtendableTestExampleExtendableSoftImplementComboClass;
        $this->assertFalse($result->isClassExtendedWith('RabbleRabbleRabble'));
        $this->assertTrue($result->isClassExtendedWith(ExtendableTestExampleBehaviorClass1::class));
        $this->assertTrue($result->isClassExtendedWith(ExtendableTestExampleBehaviorClass2::class));
        // ExtendableTestExampleBehaviorClass2 takes priority, defined last
        $this->assertEquals('bar', $result->getFoo());
    }

    public function testDotNotation()
    {
        $subject = new ExtendableTestExampleExtendableClassDotNotation();
        $subject->extendClassWith('ExtendableTest.ExampleBehaviorClass2');

        $this->assertTrue($subject->isClassExtendedWith('ExtendableTest.ExampleBehaviorClass1'));
        $this->assertTrue($subject->isClassExtendedWith('ExtendableTest.ExampleBehaviorClass2'));
    }

    public function testMethodExists()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $this->assertTrue($subject->methodExists('extend'));
    }

    public function testMethodNotExists()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $this->assertFalse($subject->methodExists('missingFunction'));
    }

    public function testDynamicMethodExists()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->addDynamicMethod('getFooAnotherWay', 'getFoo', ExtendableTestExampleBehaviorClass1::class);

        $this->assertTrue($subject->methodExists('getFooAnotherWay'));
    }

    public function testGetClassMethods()
    {
        $subject = new ExtendableTestExampleExtendableClass;
        $subject->addDynamicMethod('getFooAnotherWay', 'getFoo', ExtendableTestExampleBehaviorClass1::class);
        $methods = $subject->getClassMethods();

        $this->assertContains('extend', $methods);
        $this->assertContains('getFoo', $methods);
        $this->assertContains('getFooAnotherWay', $methods);
        $this->assertNotContains('missingFunction', $methods);
    }

    public function testIsInstanceOf()
    {
        $subject1 = new ExtendableTestExampleExtendableClass;
        $subject2 = new ExtendableTestExampleExtendableSoftImplementFakeClass;
        $subject3 = new ExtendableTestExampleExtendableSoftImplementRealClass;

        $this->assertTrue($subject1->isClassInstanceOf(ExampleExtendableInterface::class));
        $this->assertFalse($subject2->isClassInstanceOf(ExampleExtendableInterface::class));
        $this->assertTrue($subject3->isClassInstanceOf(ExampleExtendableInterface::class));
    }
}
