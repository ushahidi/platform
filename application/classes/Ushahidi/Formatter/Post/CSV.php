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

		/**
		 * Sort the columns from the heading so that they match with the record keys
		 * @DEVNOTE this is the key to solving #2028
		 */
		$heading = $this->getCSVHeading($records);
		// Send response as CSV download
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/csv; charset=utf-8');
		header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');

		$fp = fopen('php://output', 'w');

		// Add heading
		fputcsv($fp, array_values($heading));

		foreach ($records as $record)
		{
			unset($record['attributes']);

			// Transform post_date to a string
			if ($record['post_date'] instanceof \DateTimeInterface) {
				$record['post_date'] = $record['post_date']->format("Y-m-d H:i:s");
			}
			$values = [];
			foreach ($heading as $key => $value) {
				$setValue = '';
				if (isset($record[$key])){
					$setValue = $record[$key];
				} else if (isset($record['values'][$key])) {
					$setValue = array_pop($record['values'][$key]);
				} else {
					$setValue = '';
				}
				$setValue = is_array($setValue) ? json_encode($setValue) : $setValue;
				$values[] = $setValue;
			}
			fputcsv($fp, $values);
		}

		fclose($fp);

		// No need for further processing
		exit;
	}

	/**
	 * @DEVNOTE
	 * @param $fields: an array with the form: ["uuid": (value)] where value can be anything that the user chose.
	 * 								Uuid matches the ones from the $attributeSortingArray.
	 * @param $fieldsWithPriorityValue: an associative array with the form ["uuid"=>[label: string, priority: number, stage: number],"uuid"=>[label: string, priority: number, stage: number]]
	 */
	private function createSortedHeading($fields){
		$headingResult = [];
		$fieldsWithPriorityValue = [];
		foreach ($fields as $fieldKey => $fieldAttr){
			if (!is_array($fieldAttr)){
				$headingResult[$fieldKey] = $fieldAttr;
			} else {
				$fieldsWithPriorityValue[$fieldKey] = $fieldAttr;
			}
		}
		$sortedFields = [];
		/**
		 * sorting the multidimensional array of properties
		 */

		$attributeKeysWithStage = [];
		foreach ($fieldsWithPriorityValue as $attributeKey => $attribute){
			if (!array_key_exists("".$attribute["stage"], $attributeKeysWithStage)){
				$attributeKeysWithStage["".$attribute["stage"]] = [];
			}
			$attributeKeysWithStage["".$attribute["stage"]][$attributeKey] = $attribute;
		}

		$attributeKeysWithStageFlat = [];
		foreach ($attributeKeysWithStage as $stageKey => $attributeKeys){
			uasort($attributeKeys, function ($item1, $item2) {
				if ($item1['priority'] == $item2['priority']) return 0;
				return $item1['priority'] < $item2['priority'] ? -1 : 1;
			});
			foreach ($attributeKeys as $attributeKey => $attribute){
				$attributeKeysWithStageFlat[$attributeKey] = $attribute['label'];
			}
		}
		$headingResult += $attributeKeysWithStageFlat;
		return $headingResult;
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

	/**
	 * @DEVNOTE : think about possibility of dropping the reference based param. It's way too easy to mess things up with that ref
	 * @param $columns by reference .
	 * @param $key
	 * @param $label
	 * @param $value
	 */
	private function assignColumnHeading(&$columns, $key, $label, $value)
	{
		/**
		 * @DEVNOTE check multivalue fields (ie: lists/checkboxes I think)
		 */
		if (is_array($value))
		{
			// Assign in multiple columns
			foreach ($value as $sub_key => $sub_value)
			{
				$multivalue_key = $key.'.'.$sub_key;

				if (! in_array($multivalue_key, $columns))
				{
					$columns[$multivalue_key] = $label.'.'.$sub_key;
				}
			}
		}

		// ... else assign single key
		else
		{
			if (! in_array($key, $columns))
			{
				$columns[$key] = $label;
			}
		}
	}

	/**
	 * Extracts column names shared across posts to create a CSV heading, sorts them with the following criteria:
	 * - Survey "native" fields such as title from the post table go first. These are sorted alphabetically.
	 * - Form_attributes are grouped by survey, then task, and sorted in ASC order by priority
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
			$attributes = $record['attributes'];
			unset($record['attributes']);

			foreach ($record as $key => $val)
			{
				// Assign form keys
				if ($key == 'values')
				{

					foreach ($val as $key => $val)
					{
						$label = $attributes[$key]['label'];// @DEVNOTE refactoring the attributes in retrieveColumnNameData required this change
						$this->assignColumnHeading($columns, $key, $attributes[$key], $val[0]);
					}
				}

				// Assign post keys
				else
				{
					$this->assignColumnHeading($columns, $key, $key, $val);
				}
			}
		}

		return $this->createSortedHeading($columns);
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
