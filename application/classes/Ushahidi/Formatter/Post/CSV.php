<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for CSV export
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Tool\Formatter;

class Ushahidi_Formatter_Post_CSV implements Formatter
{
	/**
	 * @var SearchData
	 */
	protected $search;

	// Formatter
	public function __invoke($records)
	{
		$this->generateCSVRecords($records);
	}

	/**
	 * Generates records that are suitable to save in CSV format.
	 *
	 * Since search records will have mixed forms, rows that
	 * do not have a matching form field will be padded.
	 *
	 * @param array $records
	 *
	 * @return array
	 */
	protected function generateCSVRecords($records)
	{
		// Get CSV heading
		$heading = $this->getCSVHeading($records);

		// Sort the columns from the heading so that they match with the record keys
		sort($heading);

		// Send response as CSV download
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/csv; charset=utf-8');
		header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');

		$fp = fopen('php://output', 'w');

		// Add heading
		fputcsv($fp, $heading);

		foreach ($records as $record)
		{
			$record = $record->asArray();

			foreach ($record as $key => $val)
			{
				// Assign form values
				if ($key == 'values')
				{
					unset($record[$key]);

					foreach ($val as $key => $val)
					{
						$this->assignRowValue($record, $key, $val[0]);
					}
				}

				// Assign post values
				else
				{
					unset($record[$key]);
					$this->assignRowValue($record, $key, $val);
				}
			}

			// Pad record
			$missing_keys = array_diff($heading, array_keys($record));
			$record = array_merge($record, array_fill_keys($missing_keys, null));

			// Sort the keys so that they match with columns from the CSV heading
			ksort($record);

			fputcsv($fp, $record);
		}

		fclose($fp);

		// No need for further processing
		exit;
	}

	private function assignRowValue(&$record, $key, $value)
	{
		if (is_array($value))
		{
			// Assign in multiple columns
			foreach ($value as $sub_key => $sub_value)
			{
				$record[$key.'.'.$sub_key] = $sub_value;
			}
		}

		// ... else assign value as single string
		else
		{
			$record[$key] = $value;
		}
	}

	private function assignColumnHeading(&$columns, $key, $value)
	{
		if (is_array($value))
		{
			// Assign in multiple columns
			foreach ($value as $sub_key => $sub_value)
			{
				$multivalue_key = $key.'.'.$sub_key;

				if (! in_array($multivalue_key, $columns))
				{
					$columns[] = $multivalue_key;
				}
			}
		}

		// ... else assign single key
		else
		{
			if (! in_array($key, $columns))
			{
				$columns[] = $key;
			}
		}
	}

	/**
	 * Extracts column names shared across posts to create a CSV heading
	 *
	 * @param array $records
	 *
	 * @return array
	 */
	protected function getCSVHeading($records)
	{
		$columns = [];

		// Collect all column headings
		foreach ($records as $record)
		{
			$record = $record->asArray();

			foreach ($record as $key => $val)
			{
				// Assign form keys
				if ($key == 'values')
				{
					foreach ($val as $key => $val)
					{
						$this->assignColumnHeading($columns, $key, $val[0]);
					}
				}

				// Assign post keys
				else
				{
					$this->assignColumnHeading($columns, $key, $val);
				}
			}
		}

		return $columns;
	}

	/**
	 * Checks if value is a locaton
	 *
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function isLocation($value)
	{
		return is_array($value) &&
			array_key_exists('lon', $value) &&
			array_key_exists('lat', $value);
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
