<?php

/**
 * Ushahidi Platform DBv2 Post Import Step
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

class PostStep implements ImportStep
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
			->addMapping('incident_title', 'title')
			->addMapping('incident_description', 'content');

		// Load new user id from map
		$this->writer->setOriginalIdentifier('original_id');
		$userConverter = new CallbackValueConverter(function ($user_id) {
			if ($user_id) {
				return $this->resourceMap->getMappedId('user', $user_id);
			}
		});

		$reader = new Reader\PdoReader($options['connection'], 'SELECT * FROM incident ORDER BY id ASC');
		$workflow = new Workflow($reader, $options['logger'], 'dbv2-incidents');
		$result = $workflow
			->addWriter($this->writer)
			->addItemConverter($converter)
			->addValueConverter('user_id', $userConverter)
			->setSkipItemOnFailure(true)
			->process()
		;

		return $result;
	}
}
