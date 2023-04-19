<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\Extendable;

/*
 * Example class that has an invalid implementation
 */
class ExtendableTestInvalidExtendableClass extends Extendable
{
    public $behaviors = 24;

    public $classAttribute;
}
