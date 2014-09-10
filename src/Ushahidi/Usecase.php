<?php

/**
 * Ushahidi Platform Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

interface Usecase
{
	/**
	 * Take user input and return a result based on that input.
	 *
	 * Typically validates the data and performs one or more repository actions
	 * to deliver the requested data.
	 *
	 * @param  Data $input  context specific input data
	 * @return mixed
	 */
	public function interact(Data $input);
}
