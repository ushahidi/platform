<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for Ushahidi_Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class AuthorizerTest extends Unittest_TestCase {

	protected $repository;

	public function setUp()
	{
		parent::setUp();

		$this->acl = $this->getMock('A2', ['is_allowed']);

		$di = service();
		$proxy = $di->newFactory('Ushahidi_EntityACLResourceProxy');

		$this->authorizer = new Ushahidi_Authorizer($this->acl, $proxy);
	}

	/**
	 * Test get method
	 */
	public function test_isAllowed_fail()
	{
		$entity = new Ushahidi\Entity\Post();
		$user   = new Model_User();

		$this->acl->expects($this->any())
			->method('is_allowed')
			->with($this->identicalTo($user), $this->isInstanceOf('Ushahidi_EntityACLResourceProxy'), $this->equalTo('get'), $this->equalTo(FALSE))
			->will($this->returnValue(TRUE));

		$this->authorizer->isAllowed($entity, 'get', $user);
	}

	/**
	 * Test get method
	 *
	 * @expectedException Ushahidi\Exception\AuthorizerException
	 */
	public function test_isAllowed_pass()
	{
		$entity = new Ushahidi\Entity\Post();
		$user   = new Model_User();

		$this->acl->expects($this->any())
			->method('is_allowed')
			->with($this->identicalTo($user), $this->isInstanceOf('Ushahidi_EntityACLResourceProxy'), $this->equalTo('get'), $this->equalTo(FALSE))
			->will($this->returnValue(FALSE));

		$this->authorizer->isAllowed($entity, 'get', $user);
	}

}