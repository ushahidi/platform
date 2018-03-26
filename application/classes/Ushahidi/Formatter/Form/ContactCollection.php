<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Form Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\SearchData;

class Ushahidi_Formatter_Form_ContactCollection extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;
	
	public function __invoke($entities = [])
	{
		$data = [];
		foreach ($entities as $entity) {
			$data[] = $entity->asArray();
		}
		return $data;
	}

	/**
	 * Store paging parameters.
	 *
	 * @param  SearchData $search
	 * @param  Integer    $total
	 * @return $this
	 */
	public function setSearch(SearchData $search, $total = null)
	{
		$this->search = $search;
		$this->total  = $total;
		return $this;
	}
}
