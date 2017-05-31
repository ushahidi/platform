<?php
// @codingStandardsIgnoreFile

namespace Tests\Integration\Bootstrap;

// Load bootstrap to hook into \Kohana
require_once __DIR__.'/../../bootstrap.php';

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class KohanaContext implements SnippetAcceptingContext
{
}
