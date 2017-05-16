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

use Util_BoundingBox;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class BoundingBoxTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * Test Bounding Box to WKT
	 *
	 * @return void
	 */
	public function testToWKT()
	{
		$bb = new Util_BoundingBox(-180, -90, 180, 90);
		$this->assertEquals('POLYGON((-180 -90,180 -90,180 90,-180 90,-180 -90))', $bb->toWKT());

		$bb = new Util_BoundingBox(-1, -1, 1, 1);
		$this->assertEquals('POLYGON((-1 -1,1 -1,1 1,-1 1,-1 -1))', $bb->toWKT());
	}

	/**
	 * Test Bounding Box to array
	 *
	 * @return void
	 */
	public function testAsArray()
	{
		$bb = new Util_BoundingBox(-180, -90, 180, 90);
		$this->assertEquals(array(-180, -90, 180, 90), $bb->as_array());

		$bb = new Util_BoundingBox(-1, -1, 1, 1);
		$this->assertEquals(array(-1, -1, 1, 1), $bb->as_array());
	}

	/**
	 * Test Bounding Box to geometry
	 *
	 * @return void
	 */
	public function testToGeometry()
	{
		$bb = new Util_BoundingBox(-180, -90, 180, 90);
		$geom = $bb->toGeometry();
		$this->assertEquals('POLYGON((-180 -90,180 -90,180 90,-180 90,-180 -90))', $geom->toWKT());

		$bb = new Util_BoundingBox(-1, -1, 1, 1);
		$geom = $bb->toGeometry();
		$this->assertEquals('POLYGON((-1 -1,1 -1,1 1,-1 1,-1 -1))', $geom->toWKT());
	}
}
