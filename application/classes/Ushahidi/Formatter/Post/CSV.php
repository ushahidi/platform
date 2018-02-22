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

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;
use League\Flysystem\Util\MimeType;

class Ushahidi_Formatter_Post_CSV extends Ushahidi_Formatter_API
{
	public static $csvFieldFormat = array(
		'tags' => 'single_array',
		'sets' => 'multiple_array',
		'point'=> 'single_value_array'
	);
	/**
	 * @var SearchData
	 */
	protected $search;
	protected $fs;
	protected $tmpfname;
	protected $add_header;

	// Formatter
	public function __invoke($records)
	{
		return $this->generateCSVRecords($records);
	}

	public function setAddHeader($add_header)
	{
		$this->add_header = $add_header;
	}

	public function setFilesystem($fs)
	{
		$this->tmpfname = DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . strtolower(uniqid() . '-' . strftime('%G-%m-%d') . '.csv');
		$this->fs = $fs;
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
		//$stream = fopen('php://memory', 'w');
		$stream = tmpfile();

		/**
		 * Before doing anything, clean the ouput buffer and avoid garbage like unnecessary space paddings in our csv export
		 */
		ob_clean();

		/**
		 * Get the columns from the heading, already sorted to match the key's stage & priority.
		 */
		$headingColumns = $this->getCSVHeading($records);
		$heading = $this->createSortedHeading($headingColumns);	

		// Add heading
		if ($this->add_header) {
			fputcsv($stream, array_values($heading));
		}

		foreach ($records as $record)
		{
			// Transform post_date to a string
			if ($record['post_date'] instanceof \DateTimeInterface) {
				$record['post_date'] = $record['post_date']->format("Y-m-d H:i:s");
			}

			$values = [];

			foreach ($heading as $key => $value) {
				$values[] = $this->getValueFromRecord($record, $key);
			}
			fputcsv($stream, $values);
		}

		return $this->writeStreamToFS($stream);
	}


	private function writeStreamToFS($stream)
	{

		$filepath = implode(DIRECTORY_SEPARATOR, [
			'csv',
			$this->tmpfname,
			]);

		// Remove any leading slashes on the filename, path is always relative.
		$filepath = ltrim($filepath, DIRECTORY_SEPARATOR);

		$extension = pathinfo($filepath, PATHINFO_EXTENSION);
		
		$mimeType = MimeType::detectByFileExtension($extension) ?: 'text/plain';
		
		$config = ['mimetype' => $mimeType];
		
		$this->fs->putStream($filepath, $stream, $config);

		if (is_resource($stream)) {
			fclose($stream);
		}

		$size = $this->fs->getSize($filepath);
		$type = $this->fs->getMimetype($filepath);

		return new FileData([
			'file'   => $filepath,
			'type'   => $type,
			'size'   => $size,
			]);

	}

	private function getValueFromRecord($record, $keyParam){
		$return = '';
		$keySet = explode('.', $keyParam); //contains key + index of the key
		$headingKey = isset($keySet[0]) ? $keySet[0] : null;
		$key = isset($keySet[1]) ? $keySet[1] : null;
		$recordAttributes = isset($record['attributes'][$headingKey]) ? $record['attributes'][$headingKey] : null;
		$format = 'single_raw';
		if (is_array($recordAttributes) && isset($recordAttributes['type']) && isset(self::$csvFieldFormat[$recordAttributes['type']])){
			$format = self::$csvFieldFormat[$recordAttributes['type']];
		}
		$recordValue = isset ($record['attributes']) && $recordAttributes? $record['values']: $record;
		$isDateField = $recordAttributes['input'] === 'date' && $recordAttributes['type'] === 'datetime';

		if ($isDateField) {
			$date = new DateTime($recordValue[$headingKey][$key]);
			$recordValue[$headingKey][$key] = $date->format('Y-m-d');
		}

 		if($format === 'single_value_array'){
			/*
			 * Lat/Lon are never multivalue fields so we can get the first index  only
			 */
			$return = $this->singleValueArray($recordValue, $headingKey, $key);
		} else if ($format === 'single_array') {
			/**
			 * we need to join the array items in a single comma separated string
			 */
			$return = $this->singleColumnArray($recordValue, $headingKey);
		} else if ($format ==='multiple_array' || ($key !== null && isset($recordValue[$headingKey]) && is_array($recordValue[$headingKey]))) {
			/**
			 * we work with multiple posts which means our actual count($record[$key])
			 * value might not exist in all of the posts we are posting in the CSV
			 */
			$return = $this->multiColumnArray($recordValue, $headingKey, $key);
		} else if ($format === 'single_raw') {
			$return = $this->singleRaw($recordValue, $record, $headingKey, $key);
		}
		return $return;
	}
	private function singleRaw($recordValue, $record, $headingKey, $key){
	 	if ($key !== null) {
			return isset($recordValue[$headingKey])? ($recordValue[$headingKey]): '';
		} else {
			$emptyRecord = !isset($record[$headingKey]) || (is_array($record[$headingKey]) && empty($record[$headingKey]));
			return $emptyRecord ? '' : $record[$headingKey];
		}
	}
	private function multiColumnArray($recordValue, $headingKey, $key) {
		return isset($recordValue[$headingKey][$key])? ($recordValue[$headingKey][$key]): '';
	}
	private function singleColumnArray($recordValue, $headingKey, $separator = ',') {
		/**
	 	* we need to join the array items in a single comma separated string
	 	*/
		return isset($recordValue[$headingKey])? (implode($separator, $recordValue[$headingKey])): '';
	}
	private function singleValueArray($recordValue, $headingKey, $key) {
		/**
		 * we need to join the array items in a single comma separated string
		 */
		return isset($recordValue[$headingKey][0][$key])? ($recordValue[$headingKey][0][$key]): '';
	}

	/**
	 * @param $fields: an array with the form: ["key": (value)] where value can be anything that the user chose.
	 * @return array of sorted fields with a zero based index. Multivalue keys have the format keyxyz.index index being an arbitrary count of the amount of fields.
	 */
	private function createSortedHeading($fields){
		/**
		 * sorting the multidimensional array of properties
		 */
		/**
		 * First, group fields by stage and survey id
		 */
		$attributeKeysWithStage = $this->groupFieldsByStage($fields);
		/**
		 * After we have group by stage , we can proceed to sort each field by priority inside the stage
		 */
		$headingResult = $this->sortGroupedFieldsByPriority($attributeKeysWithStage);
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
				if ($item1['priority'] === $item2['priority']){
					/**
					 * if they are the same in priority, then that means we will fall back to alphabetical priority for them
					 */
					return $item1['label'] < $item2['label'] ? -1 : 1;
				}
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
					$singleColumnArray = (isset(self::$csvFieldFormat[$attribute['type']]) && self::$csvFieldFormat[$attribute['type']] === 'single_array');
					if ($attribute['count'] > 1 & !$singleColumnArray){
						for ($i = 0 ; $i < $attribute['count']; $i++){
							$attributeKeysWithStageFlat[$attributeKey.'.'.$i] = $attribute['label'].'.'.$i;
						}
					} else {
						$attributeKeysWithStageFlat[$attributeKey.'.0'] = $attribute['label'];
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
			$key = $attribute["form_id"]."".$attribute["stage"];
			if (!array_key_exists($key, $attributeKeysWithStage)){
				$attributeKeysWithStage[$key] = [];
			}
			$attributeKeysWithStage[$key][$attributeKey] = $attribute;
		}
		ksort($attributeKeysWithStage);
		return $attributeKeysWithStage;

	}

	/**
	 * @param $columns by reference .
	 * @param $key
	 * @param $label
	 * @param $value
	 * @param $nativeField
	 */
	private function assignColumnHeading(&$columns, $key, $labelObject, $value, $nativeField = true)
	{
		$prevColumnValue = isset($columns[$key]) ? $columns[$key]: ['count' => 0];
		$headingCount = $prevColumnValue['count'] < count($value)?  count($value) : $prevColumnValue['count'] ;
		if (!is_array($labelObject)){
			$labelObject = ['label' => $labelObject, 'count' => $headingCount, 'type' => $key, 'nativeField' => $nativeField, 'priority' => -1, 'form_id' => -1, 'stage' => -1];
		}
		$labelObject['count'] = $headingCount;
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
						$this->assignColumnHeading($columns, $key, $attributes[$key], $val, false);
					}
				}
				// Assign post keys
				else
				{
					$this->assignColumnHeading($columns, $key, $key, $val);
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
