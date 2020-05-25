<?php

/**
 * Ushahidi CSV Transformer.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Transformer;

use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Tool\MappingTransformer;

class CSVPostTransformer implements MappingTransformer
{
    const MULTIPLE_VALUES_SEPARATOR = ',';
    protected $map;
    protected $fixedValues;
    protected $repo;
    protected $unmapped;
    protected $multipleValuesAttributes = [];

    public function setRepo(PostRepository $repo)
    {
        $this->repo = $repo;
    }

    // MappingTransformer
    public function setMap(array $map)
    {
        $this->map = $map;
    }

    // MappingTransformer
    public function setFixedValues(array $fixedValues)
    {
        $this->fixedValues = $fixedValues;
    }

    public function setMultipleValuesAttributes(?array $multipleValuesAttributes): void
    {
        $this->multipleValuesAttributes = $multipleValuesAttributes ?? [];
    }

    // Transformer
    public function interact(array $record)
    {
        $record = $this->mapRecord($record);
        $record = $this->sanitizeRecord($record);

        // Filter post fields from the record
        $post_entity = $this->repo->getEntity();
        $post_fields = array_intersect_key($record, $post_entity->asArray());

        // Remove post fields from the record and leave form values
        foreach ($post_fields as $key => $val) {
            unset($record[$key]);
        }

        foreach ($record as $key => $value) {
            if ($this->isLocation($value) || ! is_array($value)) {
                $record[$key] = [$value];
            }
        }

        $form_values = [
            'values' => $record,
        ];

        return array_merge_recursive($post_fields, $form_values, $this->fixedValues);
    }

    /**
     * Maps the CSV record array to a new array using the mapped fields.
     *
     * @param array $record The CSV row as an array
     * @return array A new array with the mapped fields
     */
    private function mapRecord(array $record): array
    {
        $values = array_values($record);
        $mappedRecord = [];
        foreach ($this->map as $index => $column) {
            if ($column === null) {
                continue;
            }

            $value = trim($values[$index]);

            if ($value) {
                $mappedRecord[$column] = $value;
            }
        }

        return $mappedRecord;
    }

    /**
     * Parse the input values of the record to manageable post data.
     *
     * @param array $record Array containing the mapped fields
     * @return array
     */
    private function sanitizeRecord(array $record): array
    {
        $record = $this->parseMultipleValuesAttributes($record);
        $record = $this->parseDotNotationColumns($record);

        return $record;
    }

    /**
     * Parse the fields that have been marked as containing multiple values.
     * Example: "cat1,cat2" will be parsed to ["cat1", "cat2"].
     *
     * @param array $record
     * @return array
     */
    private function parseMultipleValuesAttributes(array $record): array
    {
        foreach ($this->multipleValuesAttributes as $attributeKey) {
            if (! isset($record[$attributeKey])) {
                continue;
            }

            $value = $record[$attributeKey];
            if (is_string($value) && strpos($value, self::MULTIPLE_VALUES_SEPARATOR) !== false) {
                $record[$attributeKey] = array_map('trim', explode(self::MULTIPLE_VALUES_SEPARATOR, $value));
            }
        }

        return $record;
    }

    /**
     * Parse dot notation fields.
     * Multi-value columns use dot notation to add sub-keys
     * e.g. 'location.lat' refers to a field called 'location'
     * and 'lat' is a sub-key of the field.
     *
     * @param array $record
     */
    private function parseDotNotationColumns(array $record): array
    {
        foreach ($record as $column => $value) {
            if (strpos($column, '.') === false) {
                continue;
            }
            array_set($record, $column, $value);
            unset($record[$column]);
        }

        return $record;
    }

    private function isLocation($value)
    {
        return is_array($value) &&
            array_key_exists('lon', $value) &&
            array_key_exists('lat', $value);
    }
}
