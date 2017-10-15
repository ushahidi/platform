<?php

namespace Tests\Unit\Core\Traits;

class MockMultidimensionalArray
{
    use \Ushahidi\Core\Traits\RecursiveImplode;

    public function doRecursiveImplode($sep, $the_array)
    {
        return $this->recursiveArrayImplode($sep, $the_array);
    }

    public function doFlattenArray($the_array)
    {
        $result = $this->flattenArray($the_array);
        \Log::instance()->add(\Log::INFO, 'Finished flattened array'.print_r($result, true));
        return $result;
    }
}
