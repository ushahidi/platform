<?php

/**
 * Ushahidi Feature Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Integration\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class FeatureContext implements SnippetAcceptingContext
{

	/** @BeforeScenario @private */
	public function makePrivate()
	{
		$config = \Kohana::$config->load('site');
		$config->set('private', true);

		$config = \Kohana::$config->load('features');
		$config->set('private.enabled', true);
	}

	/** @AfterScenario @private */
	public function makePublic()
	{
		$config = \Kohana::$config->load('site');
		$config->set('private', false);

		$config = \Kohana::$config->load('features');
		$config->set('private.enabled', false);
	}

	/**
	 * @BeforeScenario @rolesEnabled
	 **/
	public function enableRoles()
	{
		$config = \Kohana::$config->load('features');
		$config->set('roles', ['enabled' => true]);
	}

	/**
	 * @BeforeScenario @rolesDisabled
	 **/
	public function disableRoles()
	{
		$config = \Kohana::$config->load('features');
		$config->set('roles', ['enabled' => false]);
	}

	/** @BeforeScenario @webhooksEnabled */
	public function enableWebhooks()
	{
		$config = \Kohana::$config->load('features');
		$config->set('webhooks.enabled', true);
	}

	/** @AfterScenario @webhooksEnabled */
	public function disableWebhooks()
	{
		$config = \Kohana::$config->load('features');
		$config->set('webhooks.enabled', false);
	}

	/** @BeforeScenario @dataImportEnabled */
	public function enableDataImport()
	{
		$config = \Kohana::$config->load('features');
		$config->set('data-import.enabled', true);
	}

	/** @AfterScenario @dataImportEnabled */
	public function disableDataImport()
	{
		$config = \Kohana::$config->load('features');
		$config->set('data-import.enabled', false);
	}
}
