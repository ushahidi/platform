<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for Ushahidi_Repository_PostValue
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class mockDataTransformer {
	use Ushahidi\Core\Traits\DataTransformer;

	protected function getDefinition()
	{
		return [
			'date' => '*date'
		];
	}

	public function pTransform($data) {
		return $this->transform($data);
	}
}

class DataTransformerTest extends PHPUnit\Framework\TestCase {

	/**
	 * Test get method
	 */
	public function test_transformDate()
	{
		$mock = new mockDataTransformer();

		$original_date = new DateTime('2014-12-01 11:00', new DateTimeZone('UTC'));
		$date1 = $mock->pTransform(['date' => $original_date])['date'];
		$this->assertInstanceOf('DateTimeInterface', $date1);
		$this->assertNotSame($original_date, $date1);
		$this->assertEquals('2014-12-01 11:00:00', $date1->format('Y-m-d H:i:s'));

		$date2 = $mock->pTransform(['date' => '2014-11-12 10:10'])['date'];
		$this->assertInstanceOf('DateTimeInterface', $date2);
		$this->assertEquals('2014-11-12 10:10:00', $date2->format('Y-m-d H:i:s'));

		$date3 = $mock->pTransform(['date' => '2016-10-15T12:18:27+13:00'])['date'];
		$this->assertInstanceOf('DateTimeInterface', $date3);
		$this->assertEquals('2016-10-14 23:18:27', $date3->format('Y-m-d H:i:s'));
		$this->assertEquals('2016-10-14T23:18:27+00:00', $date3->format(DateTime::W3C));
	}

}
