<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the form model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class TileTest extends Unittest_TestCase {

	/**
	 * Test numTiles
	 *
	 * @return void
	 */
	public function test_numTiles()
	{
		$this->assertEquals(1, Util_Tile::numTiles(0));
		$this->assertEquals(2, Util_Tile::numTiles(1));
		$this->assertEquals(4, Util_Tile::numTiles(2));
		$this->assertEquals(256, Util_Tile::numTiles(8));
	}
	
	/**
	 * Test tileToBoundingBox
	 *
	 * @return void
	 */
	public function test_tileToBoundingBox()
	{
		$bb = Util_Tile::tileToBoundingBox(0, 0, 0);
		$this->assertAttributeEquals(85.051100, 'north', $bb, '', 0.0002);
		$this->assertAttributeEquals(-85.051100, 'south', $bb, '', 0.0002);
		$this->assertAttributeEquals(-180, 'west', $bb, '', 0.0002);
		$this->assertAttributeEquals(180, 'east', $bb, '', 0.0002);
		
		$bb = Util_Tile::tileToBoundingBox(1, 1, 1);
		$this->assertAttributeEquals(0, 'north', $bb, '', 0.0002);
		$this->assertAttributeEquals(-85.051100, 'south', $bb, '', 0.0002);
		$this->assertAttributeEquals(0, 'west', $bb, '', 0.0002);
		$this->assertAttributeEquals(180, 'east', $bb, '', 0.0002);
		
		$bb = Util_Tile::tileToBoundingBox(2, 2, 1);
		$this->assertAttributeEquals(66.5131, 'north', $bb, '', 0.0002);
		$this->assertAttributeEquals(0, 'south', $bb, '', 0.0002);
		$this->assertAttributeEquals(0, 'west', $bb, '', 0.0002);
		$this->assertAttributeEquals(90, 'east', $bb, '', 0.0002);
		
		$bb = Util_Tile::tileToBoundingBox(8, 13, 14);
		$this->assertAttributeEquals(83.026183, 'north', $bb, '', 0.0002);
		$this->assertAttributeEquals(82.853346, 'south', $bb, '', 0.0002);
		$this->assertAttributeEquals(-161.718750, 'west', $bb, '', 0.0002);
		$this->assertAttributeEquals(-160.312500, 'east', $bb, '', 0.0002);
	}
	
	/**
	 * Test tileToLon
	 *
	 * @return void
	 */
	public function test_tileToLon()
	{
		$this->assertEquals(-180, Util_Tile::tileToLon(0, 0), '', 0.0002);
		$this->assertEquals(0, Util_Tile::tileToLon(1, 1), '', 0.0002);
		$this->assertEquals(0, Util_Tile::tileToLon(2, 2), '', 0.0002);
		$this->assertEquals(-163.125000, Util_Tile::tileToLon(12, 8), '', 0.0002);
	}
	
	/**
	 * Test tileToLat
	 *
	 * @return void
	 */
	public function test_tileToLat()
	{
		$this->assertEquals(85.05112, Util_Tile::tileToLat(0, 0), '', 0.0002);
		$this->assertEquals(0, Util_Tile::tileToLat(1, 1), '', 0.0002);
		$this->assertEquals(0, Util_Tile::tileToLat(2, 2), '', 0.0002);
		$this->assertEquals(83.026183, Util_Tile::tileToLat(14, 8), '', 0.0002);
	}
}