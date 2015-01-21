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
	 * @var Formatter
	 */
	protected $formatter;

	/**
	 * @param  Formatter $formatter
	 * @return void
	 */
	public function setFormatter(Formatter $formatter)
	{
		$this->formatter = $formatter;
		return $this;
	}
}
