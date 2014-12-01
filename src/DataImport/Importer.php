<?php

/**
 * Ushahidi Platform Data Importer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport;

interface Importer
{
	/**
	 * Run a data import
	 *
	 * Typically take source info via options
	 *
	 * @param  Array  $options  context specific options
	 * @return mixed
	 */
	public function import(Array $options);
}
