<?php

/**
 * Ushahidi Parser Tool Trait
 *
 * Gives objects a method for storing an parser instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool;

trait ParserTrait
{
	/**
	 * @var Ushahidi\Tool\Parser
	 */
	protected $parser;

	/**
	 * @param  Ushahidi\Tool\Parser $parser
	 * @return void
	 */
	private function setParser(Parser $parser)
	{
		$this->parser = $parser;
	}
}
