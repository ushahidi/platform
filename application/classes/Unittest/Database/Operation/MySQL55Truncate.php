<?php

/**
 * Executes a mysql 5.5 safe truncate against all tables in a dataset.
 *
 * @package    DbUnit
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
