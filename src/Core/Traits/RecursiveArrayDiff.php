<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Based on PHP.net comment
 * @link http://php.net/manual/en/function.array-diff.php#91756
 */

namespace Ushahidi\Core\Traits;

trait RecursiveArrayDiff
{
    public function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            // TODO: revisit this from the perspective of ensuring Entities are
            // properly adherrent to the types their properties should be
            // Ensure comparison array is actually an array
            $aArray2 = is_array($aArray2) ? $aArray2 : [$aArray2];
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }
}
