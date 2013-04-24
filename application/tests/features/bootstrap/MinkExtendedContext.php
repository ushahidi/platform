<?php
use Behat\Behat\Context\BehatContext;
use Symfony\Component\Yaml\Yaml;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MinkExtension\Context\MinkContext;

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
		
		if (!preg_match($pattern, $actual)) {
			$message = sprintf('Current page "%s" does not match the regex "%s".', $actual, $pattern);
			throw new Exception($message);
		}
	}
}