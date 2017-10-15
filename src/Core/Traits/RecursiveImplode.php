<?php

/**
 * Ushahidi Platform
 *
 *
 */
namespace Ushahidi\Core\Traits;

trait RecursiveImplode
{
    public function recursiveArrayImplode($sep, $given_array)
    {
        $flat_array = $this->flattenArray($given_array);
        //\Log::instance()->add(\Log::INFO, 'Flattend Array:'.print_r($flat_array, true) );
        if (!is_array($flat_array)) {
            //\Log::instance()->add(\Log::INFO, 'Not an Array!:'.print_r($flat_array, true) );
            return print_r($flat_array, true);
        }
        return implode($sep, $flat_array);
    }

    public function flattenArray(array $givenArray)
    {
        $flat_array = [];
        if (!is_array($givenArray)) {
            array_push($flat_array, $givenArray);
            return $flat_array;
        }
        foreach ($givenArray as $key => $val) {
            if (is_array($val)) {
                $flat_array = array_merge($flat_array, $this->flattenArray($val));
                //\Log::instance()->add(\Log::INFO, 'Flattened one array level:'.print_r($givenArray, true) );
            } else {
                $flat_array = array_merge($flat_array, [$key=>$val]);
            }
        }
        return $flat_array;
    }
}
