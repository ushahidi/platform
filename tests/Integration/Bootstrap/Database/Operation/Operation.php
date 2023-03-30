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

namespace Ushahidi\Tests\Integration\Bootstrap\Database\Operation;

/**
 * Provides a basic interface and functionality for executing database
 * operations against a connection using a specific dataSet.
 */
interface Operation
{
    /**
     * Executes the database operation against the given $connection for the
     * given $dataSet.
     */
    public function execute($connection, $dataSet);
}
