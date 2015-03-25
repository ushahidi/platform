<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Stats
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Exception\FormatterException;

class Ushahidi_Formatter_Post_Stats implements Formatter
{
	/**
	 * @var SearchData
	 */
	protected $search;

	// Formatter
	public function __invoke($records)
	{
		$data = [
			'totals' => [],
			'group_by' => $this->search->group_by
		];

		// Grab just label and total from db results
		if ($this->search->timeline) {
		// For timeline, group by category, then time
			$entry = [
				'key' => $records[0]['label'],
				'values' => []
			];
			$cumulative_total = 0;
			foreach ($records as $record) {
				if ($record['label'] !== $entry['key'])
				{
					$data['totals'][] = $entry;
					$entry = [
						'key' => $record['label'],
						'values' => []
					];
					$cumulative_total = 0;
				}

				$cumulative_total = (int)$record['total'] + $cumulative_total;

				$entry['values'][] = [
					'label' => (int)$record['time_label'],
					'total' => (int)$record['total'],
					'cumulative_total' => $cumulative_total
				];
			}
			// Add final entry
			$data['totals'][] = $entry;
		} else {
		// For everything else, wrap in a group first
			$entry = [
				'key' => $this->search->group_by ? $this->search->group_by : 'all',
				'values' => []
			];
			foreach ($records as $record) {
				$entry['values'][] = [
					'label' => $record['label'],
					'total' => (int)$record['total']
				];
			}

			$data['totals'][] = $entry;
		}

		return $data;
	}

	/**
	 * Store search parameters.
	 *
	 * @param  SearchData $search
	 * @return $this
	 */
	public function setSearch(SearchData $search)
	{
		$this->search = $search;
		return $this;
	}
}
