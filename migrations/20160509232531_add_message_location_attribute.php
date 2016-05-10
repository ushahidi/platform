<?php

use Phinx\Migration\AbstractMigration;

class AddMessageLocationAttribute extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $connection = $this->getAdapter()->getConnection();
        $adapter = $this->getAdapter();

        $attrInsert = $connection->prepare(
            "INSERT INTO form_attributes (" . $adapter->quoteColumnName('key') . ",
                label, input, type, required, priority, cardinality, form_stage_id)
            VALUES
                ( 'message_location', 'Location', 'location', 'point', 0, 0, 1, NULL)
            "
        )->execute();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $connection = $this->getAdapter()->getConnection();
        $adapter = $this->getAdapter();

        $attrInsert = $connection->prepare(
            "DELETE FROM form_attributes
                WHERE " . $adapter->quoteColumnName('key') . " = 'message_location'
            "
        )->execute();

    }
}
