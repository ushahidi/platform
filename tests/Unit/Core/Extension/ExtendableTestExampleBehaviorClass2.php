<?php
namespace Tests\Unit\Core\Extension;

use Ushahidi\Core\Extension\ExtensionBase;

class ExtendableTestExampleBehaviorClass2 extends ExtensionBase
{
    public $behaviorAttribute;

    public function getFoo()
    {
        return 'bar';
    }
}
