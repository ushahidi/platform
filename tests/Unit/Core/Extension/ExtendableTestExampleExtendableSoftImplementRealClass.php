<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class with soft implement success
 */
class ExtendableTestExampleExtendableSoftImplementRealClass extends Extendable
{
    public $behaviors = ['@' . ExtendableTestExampleBehaviorClass1::class];
}
