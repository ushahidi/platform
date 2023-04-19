<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class with soft implement hybrid
 */
class ExtendableTestExampleExtendableSoftImplementComboClass extends Extendable
{
    public $behaviors = [
        ExtendableTestExampleBehaviorClass1::class,
        '@' . ExtendableTestExampleBehaviorClass2::class,
        '@RabbleRabbleRabble'
    ];
}
