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

class FormStep implements ImportStep
{
	use WriterTrait, ResourceMapTrait;

	/**
	 * Get post reader
	 * @return Ddeboer\DataImport\Reader
	 */
	protected function getReader()
	{
		return new Reader\PdoReader($options['connection'], 'SELECT * FROM form ORDER BY id ASC');
	}

	/**
	 * Item transform callback
	 * @param  Array  $item
	 * @return Array
	 */
	public function transform($item)
	{
		return [
			'original_id' => $item['id'],
			'name' => $item['form_title'],
			'description' => $item['form_description'],
			'disabled' => $item['form_active'] ? 0 : 1,
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
		$this->resourceMap->set('form', $this->writer->getMap());

		return $result;
	}
}
