<?php

/**
 * Ushahidi Platform DBv2 Tag Import Step
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
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\WriterInterface;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;

class TagStep implements ImportStep
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
			->addMapping('category_title', 'tag')
			->addMapping('category_description', 'description')
			->addMapping('category_color', 'color');

		// Load new parent id from writer
		$this->writer->setOriginalIdentifier('original_id');
		$parentConverter = new CallbackValueConverter(function ($parent_id) {
			if ($parent_id) {
				return $this->writer->getMappedId($parent_id);
			}
		});

		$reader = new Reader\PdoReader($options['connection'], 'SELECT * FROM category ORDER BY parent_id ASC, id ASC');
		$workflow = new Workflow($reader, $options['logger'], 'dbv2-users');
		$result = $workflow
			->addWriter($this->writer)
			->addItemConverter($converter)
			->addValueConverter('parent_id', $parentConverter)
			->setSkipItemOnFailure(true)
			->process()
		;

		// Save the map for future steps
		$this->resourceMap->set('tag', $this->writer->getMap());

		return $result;
	}
}
