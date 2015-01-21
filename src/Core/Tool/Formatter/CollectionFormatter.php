<?php

/**
 * Ushahidi Collection Formatter
 *
 * Takes a list of objects and formats each of them, using another formatter.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Formatter;

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Exception\FormatterException;

abstract class CollectionFormatter implements Formatter
{
	protected $formatter;

	/**
	 * @var SearchData
	 */
	protected $search;

	/**
	 * Collection formatter recursively invokes an entity-specific formatter.
	 *
	 * @param  Formatter $formatter
	 */
	public function __construct(Formatter $formatter)
	{
		$this->formatter = $formatter;
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

	// Formatter
	public function __invoke($entities)
	{
		if (!is_array($entities)) {
			throw new FormatterException('Collection formatter requries an array of entities');
		}

		$results = [];
		foreach ($entities as $entity) {
			$results[] = $this->formatter->__invoke($entity);
		}

		$output = [
			'count'   => count($results),
			'results' => $results,
		];

		if ($this->search) {
			$output += $this->getPaging();
		}

		return $output;
	}

	/**
	 * Collections are always paged, which requires pages metadata to be added
	 * to the results.
	 *
	 * @return Array
	 */
	abstract public function getPaging();
}
