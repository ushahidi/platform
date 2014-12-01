<?php

/**
 * Ushahidi Platform DBv2 User Import Step
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Importer\DBv2;

use Ushahidi\DataImport\ImportStep;
use Ushahidi\DataImporter\Writer\UserWriter;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader;
//use Ddeboer\DataImport\Writer;
//use Ddeboer\DataImport\Filter;
use Ddeboer\DataImport\Writer\WriterInterface;
// use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;

class UserStep implements ImportStep
{
	/**
	 * Writer
	 * @var Writer
	 */
	protected $writer;

	/**
	 * Set writer
	 * @param Writer $writer
	 */
	public function setWriter(WriterInterface $writer) {
		$this->writer = $writer;
	}

	/**
	 * Run a data import step
	 *
	 * @return mixed
	 */
	public function run(Array $options) {
		$converter = new MappingItemConverter();
		$converter->addMapping('name', 'realname');
		$converter->addMapping('id', 'null');

		$reader = new Reader\PdoReader($options['connection'], 'SELECT * FROM users ORDER BY id ASC');
		$workflow = new Workflow($reader, $options['logger'], 'dbv2-users');
		$result = $workflow
			->addWriter($this->writer)
			->addItemConverter($converter)
			->setSkipItemOnFailure(true)
			->process()
		;

		return $result;
	}
}
