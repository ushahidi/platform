<?php

/**
 * Unit tests for the form model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Util;

use Ushahidi\App\Util\Tile;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TileTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test numTiles
     *
     * @return void
     */
    public function testNumTiles()
    {
        $this->assertEquals(1, Tile::numTiles(0));
        $this->assertEquals(2, Tile::numTiles(1));
        $this->assertEquals(4, Tile::numTiles(2));
        $this->assertEquals(256, Tile::numTiles(8));
    }

    /**
     * Test tileToBoundingBox
     *
     * @return void
     */
    public function testTileToBoundingBox()
    {
        $bb = Tile::tileToBoundingBox(0, 0, 0);
        $this->assertEqualsWithDelta(85.051100, $bb->north, 0.0002);
        $this->assertEqualsWithDelta(-85.051100, $bb->south, 0.0002);
        $this->assertEqualsWithDelta(-180, $bb->west, 0.0002);
        $this->assertEqualsWithDelta(180, $bb->east, 0.0002);

        $bb = Tile::tileToBoundingBox(1, 1, 1);
        $this->assertEqualsWithDelta(0, $bb->north, 0.0002);
        $this->assertEqualsWithDelta(-85.051100, $bb->south, 0.0002);
        $this->assertEqualsWithDelta(0, $bb->west, 0.0002);
        $this->assertEqualsWithDelta(180, $bb->east, 0.0002);

        $bb = Tile::tileToBoundingBox(2, 2, 1);
        $this->assertEqualsWithDelta(66.5131, $bb->north, 0.0002);
        $this->assertEqualsWithDelta(0, $bb->south, 0.0002);
        $this->assertEqualsWithDelta(0, $bb->west, 0.0002);
        $this->assertEqualsWithDelta(90, $bb->east, 0.0002);

        $bb = Tile::tileToBoundingBox(8, 13, 14);
        $this->assertEqualsWithDelta(83.026183, $bb->north, 0.0002);
        $this->assertEqualsWithDelta(82.853346, $bb->south, 0.0002);
        $this->assertEqualsWithDelta(-161.718750, $bb->west, 0.0002);
        $this->assertEqualsWithDelta(-160.312500, $bb->east, 0.0002);
    }

    /**
     * Test tileToLon
     *
     * @return void
     */
    public function testTileToLon()
    {
        $this->assertEqualsWithDelta(-180, Tile::tileToLon(0, 0), 0.0002);
        $this->assertEqualsWithDelta(0, Tile::tileToLon(1, 1), 0.0002);
        $this->assertEqualsWithDelta(0, Tile::tileToLon(2, 2), 0.0002);
        $this->assertEqualsWithDelta(-163.125000, Tile::tileToLon(12, 8), 0.0002);
    }

    /**
     * Test tileToLat
     *
     * @return void
     */
    public function testTileToLat()
    {
        $this->assertEqualsWithDelta(85.05112, Tile::tileToLat(0, 0), 0.0002);
        $this->assertEqualsWithDelta(0, Tile::tileToLat(1, 1), 0.0002);
        $this->assertEqualsWithDelta(0, Tile::tileToLat(2, 2), 0.0002);
        $this->assertEqualsWithDelta(83.026183, Tile::tileToLat(14, 8), 0.0002);
    }
}
