<?php

/**
 * Ushahidi Platform DBv2 Form Import Step
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Importer\DBv2;

use Ushahidi\DataImport\ImportStep;
use Ushahidi\DataImport\WriterTrait;
use Ushahidi\DataImport\ResourceMapTrait;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Writer\WriterInterface;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;

class FormStep implements ImportStep
{
	use WriterTrait, ResourceMapTrait;

	// field_type -> attribute input map
	const field_types = [
		1 => 'text',
		2 => 'textarea',
		3 => 'date', // @todo date or datetime?,
		5 => 'radio',
		6 => 'checkboxes',
		7 => 'select',
		8 => 'divider_start',
		9 => 'divider_end'
	];

	// field_datatype -> attribute type map
	const field_datatypes = [
		'text' => 'text',
		'numeric' => 'decimal',
		'email' => 'varchar',
		'phonenumber' => 'varchar',
	];

	/**
	 * Get post reader
	 * @return Ddeboer\DataImport\Reader
	 */
	protected function getReader(\PDO $connection)
	{
		$fieldReader = new Reader\PdoReader($connection,
			"SELECT form_field.*,
				datatype.option_value AS field_datatype,
				hidden.option_value AS field_hidden,
				toggle.option_value AS field_toggle
			FROM form_field
			LEFT JOIN form_field_option datatype ON (
				datatype.form_field_id = form_field.id
				AND datatype.option_name = 'field_datatype'
			)
			LEFT JOIN form_field_option hidden ON (
				hidden.form_field_id = form_field.id
				AND hidden.option_name = 'field_hidden'
			)
			LEFT JOIN form_field_option toggle ON (
				toggle.form_field_id = form_field.id
				AND toggle.option_name = 'field_toggle'
			)
			ORDER BY form_id ASC, id ASC"
		);

		$formReader = new Reader\PdoReader($connection, 'SELECT * FROM form ORDER BY id ASC');

		// Note we have to sort by form_id and form.id in the other readers or OneToManyReader loses rows
		return new Reader\OneToManyReader($formReader, $fieldReader, 'fields', 'id', 'form_id');
	}

	protected function transformField($item)
	{
		$type = $item['field_datatype'] ? self::field_datatypes[$item['field_datatype']] : 'varchar';
		$input = $item['field_type'] ? self::field_types[$item['field_type']] : 'text';

		if (in_array($input, ['checkboxes', 'select', 'radio'])) {
			$options = explode('::', $item['field_default']);

			$default = count($options) > 1 ? $options[1] : '';
			$options = explode(',', $options[0]);
		} else {
			$default = $item['field_default'];
			$options = [];
		}

		return [
			'original_id' => $item['id'],
			'label' => $item['field_name'],
			'required' => $item['field_required'],
			'priority' => $item['field_position'],
			'default' => $default,
			'type' => $type,
			'input' => $input,
			'options' => $options
		];
	}

	/**
	 * Item transform callback
	 * @param  Array  $item
	 * @return Array
	 */
	public function transform($item)
	{
		// Add default attributes
		$attributes = [
			[
			//	'key' => 'original_id',
				'label' => 'Original ID',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'int',
				'input' => 'number',
				'options' => []
			],
			[
			//	'key' => 'date',
				'label' => 'Date',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'datetime',
				'input' => 'datetime',
				'options' => []
			],
			[
			//	'key' => 'location_name',
				'label' => 'Location Name',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'varchar',
				'input' => 'text',
				'options' => []
			],
			[
			//	'key' => 'location',
				'label' => 'Location',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'point',
				'input' => 'location',
				'options' => []
			],
			[
			//	'key' => 'verified',
				'label' => 'Verified',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'int',
				'input' => 'checkbox',
				'options' => []
			],
			[
			//	'key' => 'source',
				'label' => 'Source',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'varchar',
				'input' => 'radio',
				'options' => [
					'Unknown',
					'Web',
					'SMS',
					'Email',
					'Twitter'
				]
			],
			[
			//	'key' => 'news',
				'label' => 'News',
				'required' => 0,
				'priority' => 0,
				'default' => 0,
				'type' => 'varchar',
				'input' => 'text',
				'options' => []
			],
		];

		// Add custom attributes
		foreach ($item['fields'] as $field) {
			$attribute = $this->transformField($field);
			if ($attribute['type'] != 'divider_start' || $attribute['type'] != 'divider_end') {
				$attributes[] = $attribute;
			}
		}

		return [
			'original_id' => $item['id'],
			'name' => $item['form_title'],
			'description' => $item['form_description'],
			'disabled' => $item['form_active'] ? 0 : 1,
			'attributes' => $attributes
		];
	}

	/**
	 * Run a data import step
	 *
	 * @return mixed
	 */
	public function run(Array $options)
	{
		$this->writer->setOriginalIdentifier('original_id');

		$workflow = new Workflow($this->getReader($options['connection']), $options['logger'], 'dbv2-incidents');
		$result = $workflow
			->addWriter($this->getWriter())
			->addItemConverter(new CallbackItemConverter([$this, 'transform']))
			->setSkipItemOnFailure(true)
			->process()
		;

		// Save the map for future steps
		$map = $this->writer->getMap();
		// Map form_id=0 to same form as id 1
		// to account for old pre-form deployments
		$map[0] = $map[1];
		$this->resourceMap->set('form', $map);

		return $result;
	}
}
