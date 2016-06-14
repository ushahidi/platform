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
			'group_by' => $this->search->group_by,
			'group_by_attribute_key' => $this->search->group_by_attribute_key,
			'timeline_attribute' => $this->search->timeline_attribute
		];

		if (count($records)) {
			// Grab just label and total from db results
			if ($this->search->timeline) {
				$data['totals'] = $this->formatTimelineTotals($records);
				$data['time_interval'] = $this->search->getFilter('timeline_interval', 86400);
			} else {
				$data['totals'] = $this->formatTotals($records);
			}
		}

		return $data;
	}

	protected function formatTimelineTotals($records)
	{
		// For timeline, group by category, then time
		$totals = [];
		$entry = [
			'key' => $records[0]['label'] ? $records[0]['label'] : 'None',
			'values' => []
		];
		$cumulative_total = 0;


		foreach ($records as $record) {
			$record['label'] = $record['label'] ? $record['label'] : 'None';
			if ($record['label'] !== $entry['key'])
			{
				$totals[] = $entry;
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
		$totals[] = $entry;

		return $totals;
	}

	protected function formatTotals($records)
	{
		// For everything else, wrap in a group first
		$entry = [
			'key' => $this->search->group_by ? $this->search->group_by : 'all',
			'values' => []
		];
		foreach ($records as $record) {
			$entry['values'][] = [
				'label' => $record['label'] ? $record['label'] : 'None',
				'total' => (int)$record['total'],
				'id' => isset($record['id']) ? (int)$record['id'] : null
			];
		}

		return [$entry];
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
