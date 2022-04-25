<?php

/**
 * Ushahidi Feature Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Load bootstrap to hook into Laravel
// require_once __DIR__.'/../../bootstrap.php';


namespace Tests\Integration\Bootstrap;

use Tests\TestCase;
use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext extends TestCase implements SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        parent::setUp();
    }
}
