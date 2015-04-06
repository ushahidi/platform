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
	 * Run a data import step
	 *
	 * @return mixed
	 */
	public function run(Array $options)
	{
		$converter = new MappingItemConverter();
		$converter->addMapping('id', 'original_id')
			->addMapping('form_title', 'name')
			->addMapping('form_description', 'description')
			->addMapping('form_active', 'disabled')
			;

		$activeConverter = new CallbackValueConverter(function ($active) {
			return $active ? 0 : 1;
		});

		$this->writer->setOriginalIdentifier('original_id');

		$reader = new Reader\PdoReader($options['connection'], 'SELECT * FROM form ORDER BY id ASC');
		$workflow = new Workflow($reader, $options['logger'], 'dbv2-forms');
		$result = $workflow
			->addWriter($this->writer)
			->addItemConverter($converter)
			->addValueConverter('disabled', $activeConverter)
			->setSkipItemOnFailure(true)
			->process()
		;

		// Save the map for future steps
		$this->resourceMap->set('form', $this->writer->getMap());

		return $result;
	}
}
