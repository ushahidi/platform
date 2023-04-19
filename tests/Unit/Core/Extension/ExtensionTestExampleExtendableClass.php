<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class that has extensions enabled
 */
class ExtensionTestExampleExtendableClass extends Extendable
{
    public $behaviors = [ExtensionTestExampleBehaviorClass1::class];
}
