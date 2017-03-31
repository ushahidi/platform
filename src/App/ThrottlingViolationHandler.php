<?php

/**
 * Ushahidi throttling violation handler
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App;

use Ushahidi\Core\Exception\ThrottlingException;
use BehEh\Flaps\ViolationHandlerInterface;

class ThrottlingViolationHandler implements ViolationHandlerInterface
{
	public function handleViolation()
	{
		throw new ThrottlingException();
	}
}
