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

	// field_type -> attribute input map
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
	protected function getReader()
	{
		return
	}

	/**
	 * Item transform callback
	 * @param  Array  $item
	 * @return Array
	 */
	public function transform($item)
	{
		$type = $item['field_datatype'] ? self::field_datatypes[$item['field_datatype']] : 'varchar';
		$input = $item['field_type'] ? self::field_types[$item['field_type']] : 'text';

		// Load new form id from map
		// @todo form groups
		// $formConverter = new CallbackValueConverter(function ($form_id) {
		// 	if ($form_id) {
		// 		return $this->resourceMap->getMappedId('form', $form_id);
		// 	}
		// });

		return [
			'original_id' => $item['id'],
			'label' => $item['field_name'],
			'required' => $item['field_required'],
			'priority' => $item['field_position'],
			'default' => $item['field_default'],
			'type' => $type,
			'input' => $input,
			//'form_group_id'
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

		$workflow = new Workflow($this->getReader(), $options['logger'], 'dbv2-incidents');
		$result = $workflow
			->addWriter($this->getWriter())
			->addItemConverter(new CallbackItemConverter([$this, 'transform']))
			->setSkipItemOnFailure(true)
			->process()
		;

		// Save the map for future steps
		$this->resourceMap->set('form_attribute', $this->writer->getMap());

		return $result;
	}
}
