<?php

/**
 * Executes a mysql 5.5 safe truncate against all tables in a dataset.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2011 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */

class Unittest_Database_Operation_MySQL55Truncate extends PHPUnit_Extensions_Database_Operation_Truncate {

	public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
	{
		$connection->getConnection()->query("SET @FAKE_PREV_foreign_key_checks = @@foreign_key_checks");
		$connection->getConnection()->query("SET foreign_key_checks = 0");
		parent::execute($connection, $dataSet);
		$connection->getConnection()->query("SET foreign_key_checks = @FAKE_PREV_foreign_key_checks");
	}

}
