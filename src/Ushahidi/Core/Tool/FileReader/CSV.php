<?php

/**
 * Ushahidi CSV File Reader
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\FileReader;

use Ushahidi\Contracts\FileReader;
use Ushahidi\Contracts\ReaderFactory;
use Ushahidi\Core\Exception\ValidatorException;

class CSV implements FileReader
{
    protected $limit;

    protected $offset;

    protected $reader_factory;

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function setReaderFactory(ReaderFactory $reader_factory)
    {
        $this->reader_factory = $reader_factory;
    }

    public function process($file)
    {
        $reader = $this->reader_factory->createReader($file);

        // Filter out empty rows
        $nbColumns = count($reader->fetchOne());
        $reader->addFilter(function ($row) use ($nbColumns) {
            return count($row) == $nbColumns;
        });

        if ($this->offset) {
            $reader->setOffset($this->offset);
        }
        if ($this->limit) {
            $reader->setLimit($this->limit);
        }
        try {
            return new \ArrayIterator($reader->fetchAssoc());
        } catch (\InvalidArgumentException $invalidArgumentException) {
            if ($invalidArgumentException->getMessage() === 'The array must contain unique values') {
                throw new ValidatorException(
                    'CSV column names must be unique. Please rename any duplicate columns and try again.',
                    []
                );
            } elseif ($invalidArgumentException->getMessage() === 'The array can not be empty') {
                throw new ValidatorException(
                    'The CSV file you uploaded is empty. Please check your CSV file and try again.',
                    []
                );
            } else {
                throw $invalidArgumentException;
            }
        }
    }
}
