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
		return $this->generateCSVRecords($records);
	}

	/**
	 * Generates records that are suitable to save in CSV format.
	 * Records are padded with missing column headings as keys.
	 *
	 * @param array $records
	 *
	 * @return array
	 */
	protected function generateCSVRecords($records)
	{
		$csv_records = [];

		// Get CSV heading
		$heading = $this->getCSVHeading($records);
		
		// Sort the columns from the heading so that they match with the record keys
		sort($heading);

		// Add heading
		array_push($csv_records, $heading);

		foreach ($records as $record)
		{
			$record = $record->asArray();

			foreach ($record as $key => $val)
			{
				// Are these form values?
				if ($key === 'values')
				{
					// Remove 'values' column
					unset($record['values']);

					foreach ($val as $key => $val)
					{
						// XXX: Is this always a single value array?
						$val = $val[0];
						
						// Is it a location?
						if ($this->isLocation($val))
						{
							// then create separate lat and lon fields
							$record[$key.'.lat'] = $val['lat'];
							$record[$key.'.lon'] = $val['lon'];
						}

						// else assign value as single string or csv string
						else {
							$record[$key] = $this->valueToString($val);
						}
					}
				}

				// If not form values then assign value as single string or CSV string
				else
				{
					$record[$key] = $this->valueToString($val);
				}
			}

			// Pad record with missing column headings as keys
			$missing_keys = array_diff($heading, array_keys($record));
			$record = array_merge($record, array_fill_keys($missing_keys, null));

			// Sort the keys so that they match with columns from the CSV heading
			ksort($record);
			
			array_push($csv_records, $record);
		}

		return $csv_records;
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
				// Are these form values?
				if ($key === 'values')
				{
					foreach ($val as $key => $val)
					{
						// Get value from single value array
						$val = $val[0];
						
						// Is it a location?
						if ($this->isLocation($val))
						{
							// then create separate lat and lon columns
							array_push($columns, $key.'.lat', $key.'.lon');
						}

						// ...else add it as single column
						else
						{
							array_push($columns, $key);
						}
					}
				}

				// ...else add the key as is if not a form value key
				else
				{
					array_push($columns, $key);
				}
			}
		}

		// Finally, return a list of unique column names found in all posts
		return array_unique($columns);
	}

	/**
	 * Converts post values to strings
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */

	protected function valueToString($value)
	{
		// Convert array to csv string
		if (is_array($value)) {
			return implode(',', $value);
		}

		// or return value as string
	    return (string) $value;
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
