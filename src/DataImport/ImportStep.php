<?php

/**
 * Ushahidi Platform Import Step
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport;

interface ImportStep
{
	/**
	 * Run a data import step
	 *
	 * @param  Array  $options  context specific options
	 * @return mixed
	 */
	public function run(Array $options);
}
