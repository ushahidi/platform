<?php

use Phinx\Migration\AbstractMigration;

class FixUnstructuredPostSetFilter extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute(
            "UPDATE sets
            SET filter = '{ \"status\": \"all\", \"form\": \"none\" }'
            WHERE name = 'Unstructured posts'
            "
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
