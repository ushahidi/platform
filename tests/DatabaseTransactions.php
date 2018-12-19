<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseTransactions as LumenDatabaseTransactions;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ohanzee\DB;

/**
 * Extend Database Transactions trait to
 * - start transactions on ohanzee db
 * - start transactions on multisite and deployment dbs
 */
trait DatabaseTransactions
{

    /**
     * @param Ohanzee\Database
     */
    protected $database;

    protected $connectionsToTransact = ['mysql', 'multisite', 'deployment-0'];

    use LumenDatabaseTransactions {
        LumenDatabaseTransactions::beginDatabaseTransaction as parentBeginDatabaseTransaction;
    }

    /**
     * Handle database transactions on the specified connections.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        $this->parentBeginDatabaseTransaction();

        $this->database = $this->app->make(OhanzeeResolver::class)->connection();
        // Start a transaction
        $this->database->begin();
    }

    public function rollbackDatabaseTransaction()
    {
        $this->database->rollback();
    }

    public function tearDown()
    {
        $this->rollbackDatabaseTransaction();

        parent::tearDown();
    }

    /**
     * Assert that a given where condition exists in the database.
     *
     * We have to use a custom version because the transaction is isolated
     * to the individual connection
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string|null $onConnection
     * @return $this
     */
    protected function seeInOhanzeeDatabase($table, array $data)
    {
        $query = DB::select([DB::expr('COUNT(*)'), 'total'])
            ->from($table);

        foreach ($data as $column => $value) {
            $predicate = is_array($value) ? 'IN' : '=';
            $query->where($column, $predicate, $value);
        }

        $count = (int) $query
            ->execute($this->database)
            ->get('total', 0);

        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));
    }

    /**
     * Assert that a given where condition does not exist in the database.
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string|null $onConnection
     * @return $this
     */
    protected function notSeeInOhanzeeDatabase($table, array $data)
    {
        $query = DB::select([DB::expr('COUNT(*)'), 'total'])
            ->from($table);

        foreach ($data as $column => $value) {
            $predicate = is_array($value) ? 'IN' : '=';
            $query->where($column, $predicate, $value);
        }

        $count = (int) $query
            ->execute($this->database)
            ->get('total', 0);

        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s] that matched attributes [%s].',
            $table,
            json_encode($data)
        ));
    }

    /**
     * Assert that a given where condition matches a specific number of records.
     *
     * We have to use a custom version because the transaction is isolated
     * to the individual connection
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string|null $onConnection
     * @return $this
     */
    protected function seeCountInOhanzeeDatabase($table, array $data, $assertCount)
    {
        $query = DB::select([DB::expr('COUNT(*)'), 'total'])
            ->from($table);

        foreach ($data as $column => $value) {
            $predicate = is_array($value) ? 'IN' : '=';
            $query->where($column, $predicate, $value);
        }

        $count = (int) $query
            ->execute($this->database)
            ->get('total', 0);

        $this->assertEquals($assertCount, $count, sprintf(
            'Count in database table [%s] doesnt match for attributes [%s].',
            $table,
            json_encode($data)
        ));
    }
}
