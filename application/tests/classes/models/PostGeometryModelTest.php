<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the post model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class PostGeometryModelTest extends Unittest_Database_TestCase {

	public function setUp()
	{
		parent::setUp();

		// Hack to insert post_point value with POINT() data
		$pdo_connection = $this->getConnection()->getConnection();
		$pdo_connection->query("INSERT INTO `post_geometry` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (1, 1, 9,
				GeomFromText('MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)),
					((20 35, 45 20, 30 5, 10 10, 10 30, 20 35),
					(30 20, 20 25, 20 15, 30 20)))'));");
	}

	/**
	 * Get data set PostPointModel
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet()
	{
		return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			Kohana::find_file('tests/datasets', 'ushahidi/Base', 'yml')
		);
	}

	/**
	 * Provider for test_save_geom
	 *
	 * @access public
	 * @return array
	 */
	public function provider_save_geom()
	{
		return array(
			array(
				// Valid geom data
				array(
					'post_id' => 1,
					'form_attribute_id' => 9,
					'value' => 'POLYGON((1 1,5 1,5 5,1 5,1 1),(2 2,2 3,3 3,3 2,2 2))',
				)
			),
			array(
				// Valid geom data
				array(
					'post_id' => 1,
					'form_attribute_id' => 9,
					'value' => 'MULTIPOLYGON(((40 40,20 45,45 30,40 40)),((20 35,45 20,30 5,10 10,10 30,20 35),(30 20,20 25,20 15,30 20)))',
				)
			)
		);
	}

	/**
	 * Test Saving MySQL Geometry Data
	 *
	 * @dataProvider provider_save_geom
	 * @param array $set
	 * @return void
	 */
	public function test_save_geom($set)
	{
		$point = ORM::factory('Post_Geometry');
		$point->values($set);

		$point->check();
		$point->save();

		// ID should be an int greater than 0
		$this->assertTrue($point->saved());
		$this->assertInternalType('int', $point->id);
		$this->assertGreaterThan(0, $point->id);
		$this->assertInternalType('string', $point->value);
		$this->assertEquals($set['value'], $point->value);
	}

	/**
	 * Test Saving MySQL Point Data
	 *
	 * @param array $set
	 * @return void
	 */
	public function test_load_point()
	{
		$point = ORM::factory('Post_Geometry', 1);

		// ID should be an int greater than 0
		$this->assertInternalType('string', $point->value);
		$this->assertEquals('MULTIPOLYGON(((40 40,20 45,45 30,40 40)),((20 35,45 20,30 5,10 10,10 30,20 35),(30 20,20 25,20 15,30 20)))', $point->value);
	}

	/**
	 * Test Saving MySQL Point Data
	 *
	 * @dataProvider provider_save_geom
	 * @param array $set
	 * @return void
	 */
	public function test_update_point($set)
	{
		$point = ORM::factory('Post_Geometry', 1);
		$point->values($set);

		$point->check();
		$point->save();

		// ID should be an int greater than 0
		$this->assertTrue($point->saved());
		$this->assertInternalType('string', $point->value);
		$this->assertEquals($set['value'], $point->value);
	}

	/**
	 * Provider for test_validate_valid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_valid()
	{
		return array(
			array(
				// Valid geom data
				array(
					'post_id' => 1,
					'form_attribute_id' => 9,
					'value' => 'POLYGON((1 1,5 1,5 5,1 5,1 1),(2 2,2 3,3 3,3 2,2 2))',
				)
			),
			array(
				// Valid geom data
				array(
					'post_id' => 1,
					'form_attribute_id' => 9,
					'value' => 'MULTIPOLYGON(((40 40,20 45,45 30,40 40)),((20 35,45 20,30 5,10 10,10 30,20 35),(30 20,20 25,20 15,30 20)))',
				)
			)
		);
	}

	/**
	 * Provider for test_validate_invalid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_invalid()
	{
		return array(
			array(
				// Invalid attribute and post id
				array(
					'post_id' => 999,
					'form_attribute_id' => 999,
					'value' => 'POLYGON((1 1,5 1,5 5,1 5,1 1),(2 2,2 3,3 3,3 2,2 2))',
				)
			),
			array(
				// Invalid geometry data
				array(
					'post_id' => 1,
					'form_attribute_id' => 9,
					'value' => 'MULTIPOLYGON(((40 40,20 45,45 30,40 40)),(20 35 45 20,30 5,10 10,10 30,20 35),(30 20,20 25,20 15,30 20',
				)
			)
		);
	}

	/**
	 * Test Validate Valid Entries
	 *
	 * @dataProvider provider_validate_valid
	 * @return void
	 */
	public function test_validate_valid($set)
	{
		$geom = ORM::factory('Post_Geometry');
		$geom->values($set);

		try
		{
			$geom->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($e->errors('models')));
		}
	}

	/**
	 * Test Validate Invalid Entries
	 *
	 * @dataProvider provider_validate_invalid
	 * @return void
	 */
	public function test_validate_invalid($set)
	{
		$geom = ORM::factory('Post_Geometry');
		$geom->values($set);

		try
		{
			$geom->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}