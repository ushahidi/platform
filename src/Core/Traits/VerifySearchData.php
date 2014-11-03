<?php

/**
 * Ushahidi Verify Search Data Trait
 *
 * Gives objects one new method:
 * `verifySearchData(SearchData $search)`
 *
 * Type checking by trait, fun times.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\SearchData;

trait VerifySearchData
{
	/**
	 * Uses type hinting to ensure that the argument is search data.
	 * @param  SearchData $search
	 * @return Boolean
	 */
	private function verifySearchData(SearchData $search)
	{
		// type check is all that matters. PHP has weak kung-fu and will not allow
		// an extension of an implemented interface to be used as for type hinting.
		//
		//   interface Foo {
		//       function(Thing $arg);
		//   }
		//   interface Thing {}
		//   abstract class Fail implements Thing {}
		//   class Bar implments Foo {
		//       function(Fail $arg);
		//   }
		//
		// in this scenario, the Bar class will cause type check warning. :(
		return true;
	}
}
