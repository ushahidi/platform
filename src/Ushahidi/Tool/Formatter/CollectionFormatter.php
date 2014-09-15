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

namespace Ushahidi\Tool\Formatter;

use Ushahidi\SearchData;
use Ushahidi\Entity;
use Ushahidi\Tool\Formatter;
use Ushahidi\Exception\FormatterException;

abstract class CollectionFormatter implements Formatter
{
	protected $formatter;

	public function __construct(Formatter $formatter)
	{
		$this->formatter = $formatter;
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

		return [
			'count'   => count($results),
			'results' => $results,
		];
	}

	/**
	 * Collections are always paged, which requires paging metadata to be added
	 * to the results.
	 * @param  SearchData $input
	 * @return Array
	 */
	abstract public function getPaging(SearchData $input);
}
