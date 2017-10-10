<?php

/**
 * Ushahidi Platform
 *
 */

namespace Ushahidi\Core\Traits;

trait PostEntityComparison
{
    /*
    *   Here, we are forgoing the recursive elegance of recurisveArrayDiff
    *   for a more straightfoward, more hard-coded approach or comparing values
    *   This will only work for Posts, as it traverses known subarrays by name
    */
    public function postEntityComparison($oldPostEntityArray, $newPostEntityArray)
    {
      if (is_array($oldPostEntityArray) && is_array($newPostEntityArray))
      {

      }else{
        /// throw an exception
      }
      // sort the arrays by key?
      // then traverse from 1st level to next level, matching keys and checking values

      //  compare initial array

      //  then compare one-level down


    }

}
