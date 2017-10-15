<?php

/**
* Unit tests for RecursiveImplode trait
*
* @author     Ushahidi Team <team@ushahidi.com>
* @package    Ushahidi\Application\Tests
* @copyright  2017 Ushahidi
* @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

namespace Tests\Unit\Core\Traits;

/**
* @backupGlobals disabled
* @preserveGlobalState disabled
*/
class RecursiveImplodeTest extends \PHPUnit\Framework\TestCase
{

    /**
    * Test method
    */
    public function testFlattenArray1()
    {
        $mock = new MockMultidimensionalArray();

        $original_array1 = ['key0_level0'=>'val0_level0', 'key1_level0'=>'val1_level0',
        'keytoarray0_level0'=>[
            'key0_level1'=>'val0_level1',
            'key1_level1'=>'val1_level1',
            'key2_level1'=>'val2_level1',
            'keytoarray0_level1'=>
            ['key0_level2' => 'val0_level2']
            ]];
            $expected_array1 = array('key0_level0'=>'val0_level0',
            'key1_level0'=>'val1_level0',
            'key0_level1'=>'val0_level1',
            'key1_level1'=>'val1_level1',
            'key2_level1'=>'val2_level1',
            'key0_level2' => 'val0_level2'
        );
        $resulting_array1 = $mock->doFlattenArray($original_array1);

        \Log::instance()->add(\Log::INFO, 'Here is the expected array:'.print_r($expected_array1, true));
        \Log::instance()->add(\Log::INFO, 'Here is the resulting array:'.print_r($resulting_array1, true));
        \Log::instance()->add(\Log::INFO, 'Here is the diff between arrays:'.
        print_r(array_diff_assoc($expected_array1, $resulting_array1), true));

        $this->assertEquals(count($expected_array1), count($resulting_array1));
        $this->assertTrue(count(array_diff_assoc($expected_array1, $resulting_array1)) < 1);
    }

    /**
    */
    public function testFlattenArray2()
    {
        $mock = new MockMultidimensionalArray();
        $expected_array2 = ['i', 'am', 'not', 'an', 'associative', 'array'];
        $original_array2 = ['i', 'am', 'not', 'an', 'associative', 'array'];
        $resulting_array2 = $mock->doFlattenArray($original_array2);
        \Log::instance()->add(\Log::INFO, 'Here is the resultingarray2:'.print_r($resulting_array2, true));
        $this->assertEquals(count($expected_array2), count($resulting_array2));
        $this->assertTrue(count(array_diff($expected_array2, $resulting_array2)) == 0);
        for ($i = 0; $i < count($original_array2); $i++) {
            $this->assertEquals($expected_array2[$i], $original_array2[$i]);
        }
    }
    /**
    * Test method
    */
    public function testRecursiveImplode()
    {
        $mock = new MockMultidimensionalArray();
        $original_array1 = ['key0_level0'=>'val0_level0', 'key1_level0'=>'val1_level0',
        'keytoarray0_level0'=>[
            'key0_level1'=>'val0_level1',
            'key1_level1'=>'val1_level1',
            'key2_level1'=>'val2_level1',
            'keytoarray0_level1'=>
            ['key0_level2' => 'val0_level2']
            ]];
            $expected_string1 = "val0_level0,val1_level0,val0_level1,val1_level1,val2_level1,val0_level2";
            $sep1 = ",";

            $resulting_string1 = $mock->doRecursiveImplode($sep1, $original_array1);
            \Log::instance()->add(\Log::INFO, 'Here is the resulting string:'.$resulting_string1);
            $this->assertEquals($expected_string1, $resulting_string1);
    }
}
