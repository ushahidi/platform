<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for Ushahidi\Repository\Contact
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Contact;
use Ushahidi\Entity\ContactRepository;

class ContactRepositoryTest extends Unittest_TestCase {

	protected $repository;
	protected $contact;

	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->getMockForAbstractClass('Ushahidi\Entity\ContactRepository',
			array('get', 'add', 'remove', 'edit'));

		$this->contact = $this->getMock('Ushahidi\Entity\Contact');
	}

	public function test_get()
	{
		$this->repository->expects($this->any())
			->method('get')
			->will($this->returnValue($this->contact));

		$copy_class = get_class($this->repository);
		$copy = new $copy_class();

		$this->assertTrue(method_exists($copy, 'get'));
	}

	public function test_add()
	{
		$this->repository->expects($this->any())
			->method('add')
			->will($this->returnValue(TRUE));

		$copy_class = get_class($this->repository);
		$copy = new $copy_class();

		$this->assertTrue(method_exists($copy, 'add'));
	}

	public function test_remove()
	{
		$this->repository->expects($this->any())
			->method('remove')
			->will($this->returnValue(TRUE));

		$copy_class = get_class($this->repository);
		$copy = new $copy_class();

		$this->assertTrue(method_exists($copy, 'remove'));
	}

	public function test_edit()
	{
		$this->repository->expects($this->any())
			->method('edit')
			->will($this->returnValue(TRUE));

		$copy_class = get_class($this->repository);
		$copy = new $copy_class();

		$this->assertTrue(method_exists($copy, 'edit'));
	}

}