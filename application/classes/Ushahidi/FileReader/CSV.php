<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi CSV File Reader
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Csv\Reader;
use Ushahidi\Core\Tool\FileReader;
use Ushahidi\Core\Tool\ReaderFactory;

class Ushahidi_FileReader_CSV implements FileReader
{

	protected $limit;
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	protected $offset;
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	protected $reader_factory;
	public function setReaderFactory(ReaderFactory $reader_factory)
	{
		$this->reader_factory = $reader_factory;
	}

	public function process($file)
	{
		$reader = $this->reader_factory->createReader($file);

		// Filter out empty rows
		$nbColumns = count($reader->fetchOne());
		$reader->addFilter(function($row) use ($nbColumns) {
		    return count($row) == $nbColumns;
		});

		if ($this->offset) {
			$reader->setOffset($this->offset);
		}
		if ($this->limit) {
			$reader->setLimit($this->limit);
		}

		return new ArrayIterator($reader->fetchAssoc());
	}
}
