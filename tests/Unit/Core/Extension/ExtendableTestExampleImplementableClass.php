<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/**
 * ExtendableTestExampleImplementableClass
 */
class ExtendableTestExampleImplementableClass extends Extendable
{
    public $behaviors = [ExtendableTestExampleBehaviorClass1::class];
}
