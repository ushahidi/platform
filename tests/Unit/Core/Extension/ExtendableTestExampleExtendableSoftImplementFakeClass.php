<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class with soft implement failure
 */
class ExtendableTestExampleExtendableSoftImplementFakeClass extends Extendable
{
    public $behaviors = ['@RabbleRabbleRabble'];

    public static function getStatus()
    {
        return 'working';
    }
}
