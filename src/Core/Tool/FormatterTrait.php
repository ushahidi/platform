<?php

/**
 * Ushahidi Formatter Tool Trait
 *
 * Gives objects a method for storing an formatter instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

trait FormatterTrait
{
	/**
	 * @var Ushahidi\Core\Tool\Formatter
	 */
	protected $formatter;

	/**
	 * @param  Ushahidi\Core\Tool\Formatter $formatter
	 * @return void
	 */
	private function setFormatter(Formatter $formatter)
	{
		$this->formatter = $formatter;
	}
}
