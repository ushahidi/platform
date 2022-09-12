<?php
// @codingStandardsIgnoreFile

/**
 * Ushahidi Feature Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */


namespace Ushahidi\Tests\Integration\Bootstrap;

// Load bootstrap to hook into Laravel
require_once __DIR__ . '/../../bootstrap.php';

use Behat\Behat\Context\SnippetAcceptingContext;

class FeatureContext implements SnippetAcceptingContext
{
}
