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

namespace Ushahidi\Tests\Integration\Bootstrap\Database;

class DefaultTester extends AbstractTester
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
