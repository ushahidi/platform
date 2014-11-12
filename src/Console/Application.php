<?php

/**
 * Ushahidi Console Application
 *
 * Base class for bin/ushididi command line tool.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
	/**
	 * Basically just a proxy for addCommands, but works around being unable to
	 * pass an array of DI-created objects with Aura.DI.
	 * @param  Array $commands
	 * @return void
	 */
	public function injectCommands(Array $commands)
	{
		foreach ($commands as $command) {
			$this->add($command());
		}
	}
}
