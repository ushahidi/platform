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
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;

class FormAttributeStep implements ImportStep
{
	use WriterTrait, ResourceMapTrait;

	// field_type -> attribute.input map
	const field_types = [
		1 => 'text',
		2 => 'textarea',
		3 => 'date', // @todo date or datetime?,
		5 => 'radio',
		6 => 'checkbox',
		7 => 'select',
		8 => 'divider_start',
		9 => 'divider_end'
	];

	// datatype -> attribute.type map
	const data_types = [
		'text' => 'text',
		'numeric' => 'decimal',
		'email' => 'varchar',
		'phonenumber' => 'varchar',
	];

	/**
	 * Run a data import step
	 *
	 * @return mixed
	 */
	public function run(Array $options)
	{
		$converter = new MappingItemConverter();
		$converter
			->addMapping('id',             'original_id')
			->addMapping('field_name',     'label')
			->addMapping('field_required', 'required')
			->addMapping('field_position', 'priority')
			->addMapping('field_default',  'default')
			->addMapping('field_datatype', 'type')
			->addMapping('field_type',     'input')
			;

		$this->writer->setOriginalIdentifier('original_id');

		$reader = new Reader\PdoReader($options['connection'],
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
			ORDER BY id ASC"
		);

		// Load new form id from map
		$formConverter = new CallbackValueConverter(function ($form_id) {
			if ($form_id) {
				return $this->resourceMap->getMappedId('form', $form_id);
			}
		});

		$inputConverter = new CallbackValueConverter(function ($type) {
			if ($type) {
				return self::field_types[$type];
			}
			return 'text';
		});

		$typeConverter = new CallbackValueConverter(function ($datatype) {
			if ($datatype) {
				return self::data_types[$datatype];
			}
			return 'varchar';
		});

		$workflow = new Workflow($reader, $options['logger'], 'dbv2-forms');
		$result = $workflow
			->addWriter($this->writer)
			->addItemConverter($converter)
			->addValueConverter('form_id', $formConverter)
			->addValueConverter('input', $inputConverter)
			->addValueConverter('type', $typeConverter)
			->setSkipItemOnFailure(true)
			->process()
		;

		// Save the map for future steps
		$this->resourceMap->set('form_attribute', $this->writer->getMap());

		return $result;
	}
}
