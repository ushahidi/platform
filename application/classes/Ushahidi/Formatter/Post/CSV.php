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
		 * Get the columns from the heading, already sorted to match the key's stage & priority.
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
			// Transform post_date to a string
			if ($record['post_date'] instanceof \DateTimeInterface) {
				$record['post_date'] = $record['post_date']->format("Y-m-d H:i:s");
			}

			$values = [];
			foreach ($heading as $key => $value) {
				$setValue = '';
				$keySet = explode('.', $key); //contains key + index of the key, if any
				$headingKey = $keySet[0];
				$recordVal = isset ($record['attributes']) && isset($record['attributes'][$headingKey])? $record['values']: $record;
				if (count($keySet) > 1){
					if($keySet[1] === 'lat' || $keySet[1] === 'lon'){
						/*
						 * Lat/Lon are never multivalue fields so we can get the first index  only
						 */
						$setValue = isset($recordVal[$headingKey][0][$keySet[1]])? ($recordVal[$headingKey][0][$keySet[1]]): '';
					} else {
						/**
						 * we work with multiple posts which means our actual count($record[$key])
						 * value might not exist in all of the posts we are posting in the CSV
						 */
						$setValue = isset($recordVal[$headingKey][$keySet[1]])? ($recordVal[$headingKey][$keySet[1]]): '';
					}
				} else{
					if ( !isset($record[$headingKey]) || (is_array($record[$headingKey]) && empty($record[$headingKey]))) {
						$setValue = '';
					} else {
						$setValue = $record[$headingKey];
					}
				}
				$values[] = $setValue;
			}
			fputcsv($fp, $values);
		}

		fclose($fp);

		// No need for further processing
		exit;
	}

	/**
	 * @param $fields: an array with the form: ["key": (value)] where value can be anything that the user chose.
	 * @return array of sorted fields with a zero based index. Multivalue keys have the format keyxyz.index index being an arbitrary count of the amount of fields.
	 */
	private function createSortedHeading($fields){
		$headingResult = [];
		// fieldsWithPriorityValue: an associative array with the form ["uuid"=>[label: string, priority: number, stage: number],"uuid"=>[label: string, priority: number, stage: number]]
		$fieldsWithPriorityValue = [];
		/**
		 * Separate by fields that have custom priority and fields that do not have custom priority assigned
		 */
		foreach ($fields as $fieldKey => $fieldAttr) {
			if (!is_array($fieldAttr)) {
				$headingResult[$fieldKey] = $fieldAttr;
			} else if (is_array($fieldAttr) && isset($fieldAttr['nativeField'])){
				if ($fieldAttr['count'] === 0) {
					$headingResult[$fieldKey] = $fieldAttr['label'];
				}
				for ($i = 0 ; $i < $fieldAttr['count']; $i++){
					$headingResult[$fieldKey.'.'.$i] = $fieldAttr['label'].'.'.$i;
				}
			} else {
				$fieldsWithPriorityValue[$fieldKey] = $fieldAttr;
			}
		}
		/**
		 * Sort the non custom priority fields alphabetically, ASC (default)
		 */
		ksort($headingResult);
		/**
		 * sorting the multidimensional array of properties
		 */
		/**
		 * First, group fields by stage
		 */
		$attributeKeysWithStage = $this->groupFieldsByStage($fieldsWithPriorityValue);
		/**
		 * After we have group by stage , we can proceed to sort each field by priority inside the stage
		 */
		$attributeKeysWithStageFlat = $this->sortGroupedFieldsByPriority($attributeKeysWithStage);
		/**
		 * Add the custom priority fields to the heading array and return it, as is.
		 */
		$headingResult += $attributeKeysWithStageFlat;
		return $headingResult;
	}

	/**
	 * @param $groupedFields is an associative array with fields grouped in arrays by their stage
	 * @return array . Flat, associative. Example => ['keyxyz'=>'label for key', 'keyxyz2'=>'label for key2']
	 */
	private function sortGroupedFieldsByPriority($groupedFields){
		$attributeKeysWithStageFlat = [];
		foreach ($groupedFields as $stageKey => $attributeKeys){
			/**
			 * uasort is used here to preserve the associative array keys when they are sorted
			 */
			uasort($attributeKeys, function ($item1, $item2) {
				if ($item1['priority'] == $item2['priority']) return 0;
				return $item1['priority'] < $item2['priority'] ? -1 : 1;
			});
			/**
			 * Finally, we can flatten the array, and set the fields (key->labels) with the user-selected order.
			 */
			foreach ($attributeKeys as $attributeKey => $attribute){
				if (is_array($attribute) && isset($attribute['count']) && $attribute['type'] !== 'point'){
					/**
					 * If the attribute has a count key, it means we want to show that as key.index in the header.
					 * This is to make sure we don't miss values in multi-value fields
					 */
					for ($i = 0 ; $i < $attribute['count']; $i++){
						$attributeKeysWithStageFlat[$attributeKey.'.'.$i] = $attribute['label'].'.'.$i;
					}
				} else if (isset($attribute['type']) && $attribute['type'] === 'point'){
					$attributeKeysWithStageFlat[$attributeKey.'.lat'] = $attribute['label'].'.lat';
					$attributeKeysWithStageFlat[$attributeKey.'.lon'] = $attribute['label'].'.lon';
				}
			}
		}
		return $attributeKeysWithStageFlat;
	}
	/**
	 * @desc Group fields by their stage in the form.
	 * @param $fields
	 * @return array (associative) . Example structure => ie ['stg1'=>['att1'=> obj, 'att2'=> obj],'stg2'=>['att3'=> obj, 'att4'=> obj],]
	 *
	 */
	private function groupFieldsByStage($fields) {
		$attributeKeysWithStage = [];
		foreach ($fields as $attributeKey => $attribute){
			if (!array_key_exists("".$attribute["stage"], $attributeKeysWithStage)){
				$attributeKeysWithStage["".$attribute["stage"]] = [];
			}
			$attributeKeysWithStage["".$attribute["stage"]][$attributeKey] = $attribute;
		}
		return $attributeKeysWithStage;
	}


	/**
	 * @param $columns by reference .
	 * @param $key
	 * @param $label
	 * @param $value
	 */
	private function assignColumnHeading(&$columns, $key, $labelObject, $value)
	{
		$prevColumnValue = isset($columns[$key]) ? $columns[$key]: ['count' => 0];
		/**
		 * If $value is an array, then that might mean it has multiple values.
		 * We want to count the values to make sure we use the right key format and can return all results in the CSV
		 */
		if (is_array($value)){
			$headingCount = $prevColumnValue['count'] < count($value)?  count($value) : $prevColumnValue['count'] ;
			if (!is_array($labelObject)){
				$labelObject = ['label' => $labelObject, 'count' => $headingCount, 'type' => null, 'nativeField' => true ];
			}
			$labelObject['count'] = $headingCount;
		}
		$columns[$key] = $labelObject;
	}

	/**
	 * Extracts column names shared across posts to create a CSV heading, and sorts them with the following criteria:
	 * - Survey "native" fields such as title from the post table go first. These are sorted alphabetically.
	 * - Form_attributes are grouped by stage, and sorted in ASC order by priority
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
			$attributes = $record['attributes'];
			unset($record['attributes']);

			foreach ($record as $key => $val)
			{
				// Assign form keys
				if ($key == 'values')
				{

					foreach ($val as $key => $val)
					{
						$this->assignColumnHeading($columns, $key, $attributes[$key], $val);
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
