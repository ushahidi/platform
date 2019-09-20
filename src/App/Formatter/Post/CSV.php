<?php

/**
 * Ushahidi API Formatter for CSV export
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter\Post;

use Ushahidi\Core\SearchData;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;
use League\Flysystem\Util\MimeType;
use Ushahidi\App\Formatter\API;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CSV extends API
{

    public static $csvIgnoreFieldsByType = [
        'published_to',
        'lock',
        'parent_id',
        'locale'
    ];
    public static $csvFieldFormat = [
        'tags' => 'single_array',
        'sets' => 'single_array',
        'point' => 'single_value_array'
    ];
    /**
     * @var SearchData
     */
    protected $search;
    protected $fs;
    protected $tmpfname;
    protected $heading;
    protected $hxl_heading;
    protected $add_header = true;
    // Formatter
    public function setHxlHeading($rows)
    {
        $this->hxl_heading = $rows;
    }
    /**
     * @param $records
     * @param $job (an export job)
     * @param array $attributes (a list of attributes with key,type,priority
     * and other important features to manipulate records with
     * @return array|mixed
     */
    public function __invoke($records, $job = null, $attributes = [])
    {
        // Generate filename
        $this->tmpfname = Carbon::now()->format('Ymd') .'-'. Str::random(40) . '.csv';

        $this->createHeading($job->header_row);
        return $this->generateCSVRecords($records, $attributes);
    }

    public function generateHXLRows($header_rows, $hxl_rows)
    {
        $hxl = [];
        foreach ($header_rows as $key => $label) {
            $hxl[] = $this->generateHxlTagAndAttribute($key, $hxl_rows);
        }
        return $hxl;
    }

    /**
     * @param $form_attribute_key
     * @param $hxl_rows
     * @return string
     * Iterates through all hxl attributes and looks for one that matches the given $form_attribute_key
     */
    private function generateHxlTagAndAttribute($form_attribute_key, $hxl_rows)
    {
        $tag = '';
        //contains key + index of the key
        $key_set = explode('.', $form_attribute_key);
        // the heading type (sets, contact, title)
        $heading_key = isset($key_set[0]) ? $key_set[0] : null;
        //TODO handle multi column values when CSV starts supporting them again
        foreach ($hxl_rows as $hxl_row) {
            // checks that the hxl field matches the form attribute we received in $form_attribute_key
            // and if its a latlon field that needs extra processing, it does that before returning the tag
            if ($hxl_row['key'] === $heading_key && $this->shouldProcessLatLonTag($key_set, $hxl_row)) {
                $tag = $this->returnHXLTagAndAttributeString($tag, $hxl_row);
            }
        }
        return $tag;
    }

    /**
     * @param $key_set
     * @param $hxl_row
     * @return bool
     */
    private function shouldProcessLatLonTag($key_set, $hxl_row)
    {
        if ($hxl_row['tag_name'] !== 'geo' &&
            $hxl_row['type'] !== 'point' &&
            $hxl_row['input'] !== 'location'
        ) {
            return true;
        }
        // if it is an attribute with lat lon or it doesn't match the attribute type,
        // we should not process the tag
        $attr_type = isset($key_set[1]) ? $key_set[1] : null;
        $attribute_type_matches_hxl_row_type = $hxl_row['attribute'] === $attr_type;
        $is_geo_other = $hxl_row['attribute'] !== 'lat' && $hxl_row['attribute'] !== 'lon';
        return $attribute_type_matches_hxl_row_type || $is_geo_other;
    }

    /**
     * @param $tag
     * @param $hxl_row
     * @return string
     * Creates the #tag +attribute string for the hxl tags in a csv
     */
    private function returnHXLTagAndAttributeString($tag, $hxl_row)
    {
        if ($tag === '') {
            $tag = '#' . $hxl_row['tag_name'];
        }
        $attribute = $hxl_row['attribute'] && !empty($hxl_row['attribute']) ?
            ' +'. $hxl_row['attribute'] : '';
        return $tag . $attribute;
    }

    public function setAddHeader($add_header)
    {
        $this->add_header = $add_header;
    }


    public function setHeading($heading)
    {
        $this->heading = $heading;
    }

    public function setFilesystem($fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param $attributes
     * @param $records
     * @return array
     * Attributes are sorted with this criteria:
     * - Survey "native" fields such as title from the post table go first.
     *   These are sorted alphabetically.
     * - Form_attributes are grouped by stage, and sorted in ASC order by priority
     */
    public function createHeading($attributes)
    {
        $this->heading = $this->createSortedHeading($attributes);
        return $this->heading;
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
    protected function generateCSVRecords($records, $attributes)
    {
        $stream = tmpfile();
        /**
         * Before doing anything, clean the ouput buffer and avoid garbage like unnecessary space
         * paddings in our csv export
         */
        if (ob_get_length()) {
            ob_clean();
        }

        // Add heading
        if ($this->add_header) {
            fputcsv($stream, array_values($this->heading));
            fputcsv($stream, array_values($this->hxl_heading));
        }


        foreach ($records as $record) {
            $values = $this->formatRecordForCSV($record, $attributes);
            fputcsv($stream, $values);
        }

        return $this->writeStreamToFS($stream);
    }

    /**
     * @param $record
     * @param $attributes
     * @return array
     */
    public function formatRecordForCSV($record, $attributes)
    {
        // Transform post_date to a string
        if (isset($record['post_date']) && $record['post_date'] instanceof \DateTimeInterface) {
            $record['post_date'] = $record['post_date']->format("Y-m-d H:i:s");
        }
        // Transform post_date to a string
        if (isset($record['created']) && is_numeric($record['created'])) {
            $record['created'] = date("Y-m-d H:i:s", $record['created']);
        }
        if (isset($record['updated']) && is_numeric($record['updated'])) {
            $record['updated'] = date("Y-m-d H:i:s", $record['updated']);
        }

        $values = [];

        foreach ($this->heading as $key => $value) {
            $values[] = $this->getValueFromRecord($record, $key, $attributes);
        }
        return $values;
    }

    private function writeStreamToFS($stream)
    {

        $filepath = implode(DIRECTORY_SEPARATOR, [
            config('media.csv_batch_prefix', 'csv'),
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
            'file' => $filepath,
            'type' => $type,
            'size' => $size,
        ]);
    }

    /**
     * @param $record
     * @param $keyParam
     * @param $attributes
     * @return string
     * Receives a record, the field to look up, and the full list of available attributes
     * Returns the correct value with the expected format for all fields in a post
     */
    private function getValueFromRecord($record, $keyParam, $attributes)
    {
        // assume it's empty since we go through this for all attributes which might not be available
        $return = '';
        $should_return = false;
        // the $keyParam is the key=>label we get in createSortedHeading (keyLabel.index)
        $keySet = explode('.', $keyParam); //contains key + index of the key
        $headingKey = isset($keySet[0]) ? $keySet[0] : null; // the heading type (sets, contact, title)
        $key = isset($keySet[1]) ? $keySet[1] : null; // the key to use (0, lat,lon)
        // check that the key we received is available in $attributes
        $recordAttributes = isset($attributes[$headingKey]) ? $attributes[$headingKey] : null;
        // If the returned attribute for the given heading key is the native form name attribute
        // Retrieve Form Name from the attribute rather than from the Post until the data model improves

        if (is_array($recordAttributes) && isset($recordAttributes['type'])
            && $recordAttributes['type'] === 'form_name') {
            return is_array($record) && isset($record['form_name']) ? $record['form_name'] : 'Unstructured';
        }
        // Ignore attributes that are not related to this Post by Form Id
        // Ensure that native attributes identified via id 0 are included
        if (is_array($recordAttributes) && isset($recordAttributes['form_id']) && isset($record['form_id'])
            && $recordAttributes['form_id'] != 0 && ($record['form_id'] != $recordAttributes['form_id'])) {
            $should_return = true;
        }

        // Check if we are dealing with a structured post but not a structured attribute
        if (is_array($recordAttributes) && isset($recordAttributes['unstructured'])
            && $recordAttributes['unstructured'] && isset($record['form_id'])) {
            $should_return = true;
        }
        // Check if we are dealing with twitter
        if (is_array($recordAttributes) && isset($recordAttributes['unstructured'])
            && $recordAttributes['unstructured'] && isset($record['source']) && $record['source'] === 'twitter') {
            $should_return = true;
        }

        // Check if we're dealing with an unstructured post but a structured attribute
        if (!isset($record['form_id'])
            && isset($recordAttributes['form_id']) && $recordAttributes['form_id'] != 0) {
            $should_return = true;
        }

        if ($should_return) {
            return '';
        }

        // default format we will return. See $csvFieldFormat for a list of available formats
        $format = 'single_raw';
        // if we have an attribute and can find a format for it in $csvFieldFormat, reset the $format
        if (is_array($recordAttributes) && isset($recordAttributes['type'])
            && isset(self::$csvFieldFormat[$recordAttributes['type']])) {
            $format = self::$csvFieldFormat[$recordAttributes['type']];
        }

        /**
         * Remap Title and Description type attributes as these are a special case of attributes
         * since their labels are stored as attributes but their values are stored as fields on the record :/
         * The Key UUID will not match the equivalent field on the Post so we must change to use the correct field names
         */
        if (is_array($recordAttributes) && isset($recordAttributes['type'])
            && ($recordAttributes['type'] === 'title'   || $recordAttributes['type'] === 'description')) {
            // Description must be mapped to content
            // Title is title
            $headingKey = $recordAttributes['type'] === 'title' ? 'title' : 'content';
        }

        /** check if the value is in [values] (user added attributes),
         ** otherwise it'll be part of the record itself
         **/
        $recordValue = isset($record['values']) && isset($record['values'][$headingKey]) ? $record['values'] : $record;

        // handle values that are dates to have consistent formatting
        $isDateField = $recordAttributes['input'] === 'date' && $recordAttributes['type'] === 'datetime';
        if ($isDateField && isset($recordValue[$headingKey])) {
            $date = new \DateTime($recordValue[$headingKey][$key]);
            $recordValue[$headingKey][$key] = $date->format('Y-m-d');
        }
        /**
         * We have 3 formats. A single value array is only a lat/lon right now but would be usable
         * for other formats where we have a specific way to separate their fields in columns
         */
        if ($format === 'single_value_array') {
            /*
             * Lat/Lon are never multivalue fields so we can get the first index  only
             */
            $return = $this->singleValueArray($recordValue, $headingKey, $key);
        } elseif ($format === 'single_array' ||
            ($key !== null && isset($recordValue[$headingKey]) && is_array($recordValue[$headingKey]))) {
            /**
             * A single_array is a comma separated list of values (like categories) in a column
             * we need to join the array items in a single comma separated string.
             * We handle all arryas as singles at the moment
             */
            $return = $this->singleColumnArray($recordValue, $headingKey);
        } elseif ($format === 'single_raw') {
            /**
             * Single_raw is the literal representation of the value and
             * not usable for types where it's possible to have an array
             */
            $return = $this->singleRaw($recordValue, $record, $headingKey, $key);
        }
        return $return;
    }

    private function singleRaw($recordValue, $record, $headingKey, $key)
    {
        if ($key !== null) {
            return isset($recordValue[$headingKey]) ? ($recordValue[$headingKey]) : '';
        } else {
            $emptyRecord = !isset($record[$headingKey])
                || (is_array($record[$headingKey]) && empty($record[$headingKey]));
            return $emptyRecord ? '' : $record[$headingKey];
        }
    }

    private function multiColumnArray($recordValue, $headingKey, $key)
    {
        return isset($recordValue[$headingKey][$key]) ? ($recordValue[$headingKey][$key]) : '';
    }

    private function singleColumnArray($recordValue, $headingKey, $separator = ',')
    {
        /**
         * we need to join the array items in a single comma separated string
         */
        return isset($recordValue[$headingKey]) ? (implode($separator, $recordValue[$headingKey])) : '';
    }

    private function singleValueArray($recordValue, $headingKey, $key)
    {
        /**
         * we need to join the array items in a single comma separated string
         */
        return isset($recordValue[$headingKey][0][$key]) ? ($recordValue[$headingKey][0][$key]) : '';
    }

    /**
     * @param $fields : an array with the form: ["key": (value)] where value can be anything that the user chose.
     * @return array of sorted fields with a zero based index. Multivalue keys have the format keyxyz.index
     * index being an arbitrary count of the amount of fields.
     */
    private function createSortedHeading($fields)
    {
        /**
         * sort each field by priority inside the stage
         */
        return $this->sortGroupedFieldsByPriority($fields);
    }

    /**
     * @param $groupedFields is an associative array with fields grouped in arrays by their stage
     * @return array . Flat, associative. Example => ['keyxyz'=>'label for key', 'keyxyz2'=>'label for key2']
     */
    private function sortGroupedFieldsByPriority($fields)
    {

        $groupedFields = [];
        foreach ($fields as $key => $item) {
            $groupedFields[$item['form_id'] . $item['form_stage_priority'] . $item['priority']][$item['key']] = $item;
        }

        ksort($groupedFields, SORT_NUMERIC);

        $attributeKeysWithStageFlat = [];
        foreach ($groupedFields as $stageKey => $attributeKeys) {
            /**
             * uasort is used here to preserve the associative array keys when they are sorted
             */
            uasort($attributeKeys, function ($item1, $item2) {
                if ($item1['priority'] === $item2['priority']) {
                    /**
                     * if they are the same in priority, then that means we will fall back to alphabetical
                     * priority for them
                     */
                    return $item1['label'] < $item2['label'] ? -1 : 1;
                }
                return $item1['priority'] < $item2['priority'] ? -1 : 1;
            });
            /**
             * Finally, we can flatten the array, and set the fields (key->labels) with the user-selected order.
             */
            foreach ($attributeKeys as $attributeKey => $attribute) {
                if (is_array($attribute) && $attribute['type'] !== 'point') {
                    /**
                     * key=>label mapping with index[0] for regular fields
                     */
                    $attributeKeysWithStageFlat[$attributeKey . '.0'] = $attribute['label'];
                } elseif (isset($attribute['type']) && $attribute['type'] === 'point') {
                    /**
                     * key=>label mapping with lat/lon for point type fields
                     */
                    $attributeKeysWithStageFlat[$attributeKey . '.lat'] = $attribute['label'] . '.lat';
                    $attributeKeysWithStageFlat[$attributeKey . '.lon'] = $attribute['label'] . '.lon';
                }
            }
        }
        return $attributeKeysWithStageFlat;
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
        return is_array($value) && array_key_exists('lon', $value) &&
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
