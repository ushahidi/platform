<?php
/*
 * This file adapted from the DbUnit package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * (c) Ushahidi Team <team@ushahidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ushahidi\Tests\Integration\Bootstrap\Database\Dataset;

class DefaultTableIterator
{
    protected $tables;

    protected $reverse;

    public function __construct(array $tables, $reverse = false)
    {
        $this->tables  = $tables;
        $this->reverse = $reverse;

        $this->rewind();
    }

    public function getTable()
    {
        return $this->current();
    }

    public function getTableMetaData()
    {
        return $this->current()->getTableMetaData();
    }

    public function current()
    {
        return \current($this->tables);
    }

    public function key()
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    public function next()
    {
        if ($this->reverse) {
            \prev($this->tables);
        } else {
            \next($this->tables);
        }
    }

    public function rewind()
    {
        if ($this->reverse) {
            \end($this->tables);
        } else {
            \reset($this->tables);
        }
    }

    public function valid()
    {
        return $this->current() !== false;
    }
}
