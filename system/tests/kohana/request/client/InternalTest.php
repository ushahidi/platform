<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * Unit tests for internal request client
 *
 * @group kohana
 * @group kohana.core
 * @group kohana.core.request
 * @group kohana.core.request.client
 * @group kohana.core.request.client.internal
 *
 * @package    Kohana
 * @category   Tests
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Request_Client_InternalTest extends Unittest_TestCase
{
	public function provider_exceptions()
	{
		return array(
			array('', 'welcome', 'missing_action', 'welcome/missing_action',
				  'The requested URL welcome/missing_action was not found on this server.'),
			array('kohana3', 'missing_controller', 'index', 'kohana3/missing_controller/index',
				  'The requested URL kohana3/missing_controller/index was not found on this server.'),
			array('', 'template', 'missing_action', 'kohana3/template/missing_action',
				  'Cannot create instances of abstract controller_template'),
		);
	}

	/**
	 * Tests for correct exception messages
	 *
	 * @test
	 * @dataProvider provider_exceptions
	 *
	 * @return null
	 */
	public function test_exceptions($directory, $controller, $action, $uri, $expected)
	{
		// Mock for request object
		$request = $this->getMock('Request', array('directory', 'controller', 'action', 'uri', 'response'), array($uri));

		$request->expects($this->any())
			->method('directory')
			->will($this->returnValue($directory));

		$request->expects($this->any())
			->method('controller')
			->will($this->returnValue($controller));

		$request->expects($this->any())
			->method('action')
			->will($this->returnValue($action));

		$request->expects($this->any())
			->method('uri')
			->will($this->returnValue($uri));

		$request->expects($this->any())
			->method('response')
			->will($this->returnValue($this->getMock('Response')));

		$internal_client = new Request_Client_Internal;

		try
		{
			$internal_client->execute($request);
		}
		catch(HTTP_Exception_404 $e)
		{
			if ($e->getMessage() !== $expected)
			{
				$this->fail('Was expecting "'.$expected.'" but got "'.$e->getMessage().'" instead.');
			}
			return;
		}
		catch(Kohana_Exception $e)
		{
			if ($e->getMessage() !== $expected)
			{
				$this->fail('Was expecting "'.$expected.'" but got "'.$e->getMessage().'" instead.');
			}
			return;
		}

		$this->fail('A HTTP_Exception_404 or Kohana_Exception exception was expected.');
	}
}