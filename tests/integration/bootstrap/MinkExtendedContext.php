<?php
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;
use Symfony\Component\Yaml\Yaml;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Driver\GoutteDriver;

/**
 * MinkExtendedContext context.
 */
class MinkExtendedContext extends MinkContext
{

	/**
	 * @Then /^the full url should match (?P<pattern>"([^"]|\\")*")$/
	 */
	public function assertFullUrlRegExp($pattern)
	{
		$pattern = $this->fixStepArgument($pattern);

		$actual = $this->getSession()->getCurrentUrl();

		if (!preg_match($pattern, $actual))
		{
			$message = sprintf('Current page "%s" does not match the regex "%s".', $actual, $pattern);
			throw new Exception($message);
		}
	}

	/**
	 * Can Mink driver intercept redirects
	 */
	public function canIntercept()
	{
		$driver = $this->getSession()->getDriver();
		if (!$driver instanceof GoutteDriver)
		{
			throw new UnsupportedDriverActionException(
				'You need to tag the scenario with ' .
				'"@mink:goutte" or "@mink:symfony". ' .
				'Intercepting the redirections is not ' .
				'supported by %s', $driver
			);
		}
	}
	
	/**
	 * @Given /^(.*) without redirection$/
	 */
	public function theRedirectionsAreIntercepted($step)
	{
		$this->canIntercept();
		$this->getSession()->getDriver()->getClient()->followRedirects(false);
	
		return new Step\Given($step);
	}
	
	/**
	 * @Given /^(.*) with redirection$/
	 */
	public function theRedirectionsAreFollowed($step)
	{
		$this->canIntercept();
		$this->getSession()->getDriver()->getClient()->followRedirects(true);
	
		return new Step\Given($step);
	}
	
	/**
	 * @When /^I follow the redirection$/
	 * @Then /^I should be redirected$/
	 */
	public function iFollowTheRedirection()
	{
		$this->canIntercept();
		$client = $this->getSession()->getDriver()->getClient();
		$client->followRedirects(true);
		$client->followRedirect();
	}
	
	/**
	 * @Then /^the redirect location should match (?P<pattern>"([^"]|\\")*")$/
	 */
	public function redirectLocationShouldMatch($pattern)
	{
		$this->canIntercept();
		$client = $this->getSession()->getDriver()->getClient();
		$actual = $client->getResponse()->getHeader('Location');
		
		$pattern = $this->fixStepArgument($pattern);

		if (!preg_match($pattern, $actual))
		{
			$message = sprintf('Redirect location "%s" does not match the regex "%s".', $actual, $pattern);
			throw new Exception($message);
		}
	}

	/**
	 * @Then /^I should have cookie "(?P<cookie>[^"]*)"$/
	 */
	public function iShouldHaveCookie($cookie)
	{
		$this->assertSession()->cookieExists($cookie);
	}

}
